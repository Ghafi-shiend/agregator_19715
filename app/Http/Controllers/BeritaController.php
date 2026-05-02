<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BeritaController extends Controller
{
    private string $feedUrl = 'https://smkn1bawang.sch.id/feed/';

    public function index(Request $request)
    {
        $keyword = $request->keyword;

        $query = Berita::query();

        if ($keyword) {
            $query->where('judul', 'like', '%' . $keyword . '%')
                  ->orWhere('deskripsi', 'like', '%' . $keyword . '%')
                  ->orWhere('kategori', 'like', '%' . $keyword . '%');
        }

        $beritas = $query->orderByDesc('tanggal_publish')
                         ->paginate(10)
                         ->withQueryString();

        return view('berita.index', compact('beritas', 'keyword'));
    }

    public function sync()
    {
        try {
            $response = Http::withOptions([
                'verify' => false // 🔥 penting untuk error SSL kamu
            ])->timeout(20)->get($this->feedUrl);

            libxml_use_internal_errors(true);
            $xml = simplexml_load_string($response->body(), 'SimpleXMLElement', LIBXML_NOCDATA);

            if (!$xml || !isset($xml->channel->item)) {
                return redirect()->route('berita.index')
                    ->with('error', 'Feed tidak bisa dibaca');
            }

            $jumlah = 0;

            foreach ($xml->channel->item as $item) {

                $link = (string)$item->link;
                $judul = (string)$item->title;

                if (!$link || !$judul) continue;

                Berita::updateOrCreate(
                    ['link' => $link],
                    [
                        'judul' => $judul,
                        'deskripsi' => strip_tags((string)$item->description),
                        'author' => null,
                        'kategori' => null,
                        'tanggal_publish' => date('Y-m-d H:i:s', strtotime($item->pubDate)),
                    ]
                );

                $jumlah++;
            }

            return redirect()->route('berita.index')
                ->with('success', 'Berhasil sync: ' . $jumlah . ' berita');

        } catch (\Throwable $e) {
            return redirect()->route('berita.index')
                ->with('error', $e->getMessage());
        }
    }

    public function truncate()
    {
        Berita::truncate();

        return redirect()->route('berita.index')->with('success', 'Semua data dihapus');
    }

    public function edit($id)
    {
        $berita = Berita::findOrFail($id);
        return view('berita.edit', compact('berita'));
    }

    public function update(Request $request, $id)
    {
        $berita = Berita::findOrFail($id);

        $berita->update([
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->route('berita.index')->with('success', 'Berhasil update');
    }

    public function destroy($id)
    {
        $berita = Berita::findOrFail($id);
        $berita->delete();

        return redirect()->route('berita.index')->with('success', 'Berhasil hapus');
    }
}