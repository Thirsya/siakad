<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Mahasiswa_MataKuliah;
use App\Models\Kelas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PDF;

class MahasiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // fungsi eloquent menampilkan data menggunakan pagination
        // $mahasiswa = Mahasiswa::all(); // Mengambil semua isi tabel
        // $paginate = Mahasiswa::orderBy('id_mahasiswa', 'asc')->paginate(3);
        // return view('mahasiswa.index', ['mahasiswa' => $mahasiswa,'paginate'=>$paginate]);

        // if (request('search')) {
        //     $paginate = Mahasiswa::where('nama', 'like', '%' . request('search') . '%')->paginate(5);
        //     return view('mahasiswa.index', ['paginate'=>$paginate]);
        // } else {
        // $mahasiswa = Mahasiswa::all(); // Mengambil semua isi tabel
        // $paginate = Mahasiswa::orderBy('id_mahasiswa', 'asc')->paginate(5);
        // return view('mahasiswa.index', ['mahasiswa' => $mahasiswa,'paginate'=>$paginate]);
        // }

        //yang semula Mahasiswa::all, diubah menjadi with() yang menyatakan relasi
        $mahasiswa = Mahasiswa::with('kelas')->get();
        $paginate = Mahasiswa::orderBy('id_mahasiswa', 'asc')->paginate(3);
        return view('mahasiswa.index', ['mahasiswa' => $mahasiswa,'paginate'=>$paginate]);
    }

    public function create()
    {
        //return view('mahasiswa.create');
        $kelas = Kelas::all(); //mendapatkan data dari tabel kelas
        return view('mahasiswa.create',['kelas' => $kelas]);
    }

    public function store(Request $request)
    {
        //melakukan validasi data
        $request->validate([
            'Nim' => 'required',
            'Nama' => 'required',
            'Kelas' => 'required',
            'Jurusan' => 'required',
            'Alamat' => 'required',
            'Email' => 'required',
            'Tanggal_Lahir' => 'required',
            'Jenis_Kelamin' => 'required',
            'foto' => 'required',
        ]);

        $image_name = '';
        if ($request->file('foto')) {
            $image_name = $request->file('foto')->store('images', 'public');
        }

        $mahasiswa = new Mahasiswa;
        $mahasiswa->nim = $request->get('Nim');
        $mahasiswa->nama = $request->get('Nama');
        $mahasiswa->jurusan = $request->get('Jurusan');
        $mahasiswa->alamat = $request->get('Alamat');
        $mahasiswa->email = $request->get('Email');
        $mahasiswa->tanggal_lahir = $request->get('Tanggal_Lahir');
        $mahasiswa->jenis_kelamin = $request->get('Jenis_Kelamin');
        $mahasiswa->foto = $image_name;
        //$mahasiswa->save();

        $kelas = new Kelas;
        $kelas->id = $request->get('Kelas');

        //fungsi eloquent untuk menambah data dengan relasi belongsTo
        $mahasiswa->kelas()->associate($kelas);
        $mahasiswa->save();

        //fungsi eloquent untuk menambah data
        //Mahasiswa::create($request->all());//jika data berhasil ditambahkan, akan kembali ke halaman utama
        return redirect()->route('mahasiswa.index')
            ->with('success', 'Mahasiswa Berhasil Ditambahkan');
    }

    public function show($nim)
    {
        //menampilkan detail data dengan menemukan/berdasarkan Nim Mahasiswa
        //$Mahasiswa = Mahasiswa::where('nim', $nim)->first();
        // $Mahasiswa = Mahasiswa::where('nim', $nim)->first();
        // return view('mahasiswa.detail', compact('Mahasiswa'));
        $mahasiswa = Mahasiswa::with('kelas')->where('nim', $nim)->First();
        return view('mahasiswa.detail', ['Mahasiswa' => $mahasiswa]);
    }

    public function edit($nim)
    {
        //menampilkan detail data dengan menemukan berdasarkan Nim Mahasiswa untuk diedit
        // $Mahasiswa = DB::table('mahasiswa')->where('nim', $nim)->first();
        // return view('mahasiswa.edit', compact('Mahasiswa'));
        $mahasiswa = Mahasiswa::with('kelas')->where('nim', $nim)->first();
        $kelas = Kelas::all(); //mendapatakn data dari tabel kelas
        return view('mahasiswa.edit', compact('mahasiswa','kelas'));
    }

    public function update(Request $request, $nim)
    {
        //melakukan validasi data
        $request->validate([
            'Nim' => 'required',
            'Nama' => 'required',
            'Kelas' => 'required',
            'Jurusan' => 'required',
            'Alamat' => 'required',
            'Email' => 'required',
            'Tanggal_Lahir' => 'required',
            'Jenis_Kelamin' => 'required',
            'foto' => 'required',
        ]);

        //fungsi eloquent untuk mengupdate data inputan kita
        // Mahasiswa::where('nim', $nim)->update([
        //     'nim'=>$request->Nim,
        //     'nama'=>$request->Nama,
        //     'kelas'=>$request->Kelas,
        //     'jurusan'=>$request->Jurusan,
        //     'alamat'=>$request->Alamat,
        //     'email'=>$request->Email,
        //     'tanggal_lahir'=>$request->Tanggal_Lahir,
        //     'jenis_kelamin'=>$request->Jenis_Kelamin,         
        // ]);

        $mahasiswa = Mahasiswa::with('kelas')->where('nim', $nim)->first();
        $mahasiswa->nim = $request->get('Nim');
        $mahasiswa->nama = $request->get('Nama');
        $mahasiswa->jurusan = $request->get('Jurusan');
        $mahasiswa->alamat = $request->get('Alamat');
        $mahasiswa->email = $request->get('Email');
        $mahasiswa->tanggal_lahir = $request->get('Tanggal_Lahir');
        $mahasiswa->jenis_kelamin = $request->get('Jenis_Kelamin');
        
        if ($mahasiswa->foto && file_exists(storage_path('app/public/'. $mahasiswa->foto))) {
            Storage::delete('public/'. $mahasiswa->foto);
        }

        //   $image_name = '';
        if ($request->file('foto')) {
            $image_name = $request->file('foto')->store('images', 'public');
        }

        $mahasiswa->foto = $image_name;
        // $mahasiswa->save();

        $kelas = new Kelas;
        $kelas->id = $request->get('Kelas');
        
        //Fungsi eloquent untuk menambah data dengan relasi belongsTo
        $mahasiswa->kelas()->associate($kelas);
        $mahasiswa->save();
        
        //jika data berhasil diupdate, akan kembali ke halaman utama
        return redirect()->route('mahasiswa.index')
            ->with('success', 'Mahasiswa Berhasil Diupdate');
    }

    public function destroy( $nim)
    {
        //fungsi eloquent untuk menghapus data
        Mahasiswa::where('nim', $nim)->delete();return redirect()->route('mahasiswa.index')
            -> with('success', 'Mahasiswa Berhasil Dihapus');
    }

    public function Mahasiswa_MataKuliah($Nim)
    {
        $mahasiswa = Mahasiswa_MataKuliah::with('matakuliah')->where('mahasiswa_id', $Nim)->get();
        $mahasiswa->mahasiswa = Mahasiswa::with('kelas')->where('id_mahasiswa', $Nim)->first();
        return view('mahasiswa.nilai', ['mahasiswa' => $mahasiswa]);
    }

    public function nilai_pdf($nim){
        // dd('tetsing');
        $Mahasiswa = Mahasiswa::where('nim', $nim)->first();
        $nilai = Mahasiswa_MataKuliah::where('mahasiswa_id', $Mahasiswa->id_mahasiswa)
                                       ->with('matakuliah')
                                       ->with('mahasiswa')
                                       ->get();
        $nilai->mahasiswa = Mahasiswa::with('kelas')->where('nim', $nim)->first();
        $pdf = PDF::loadview('mahasiswa.nilai_pdf', compact('nilai'));
        return $pdf->stream();
    }

}
