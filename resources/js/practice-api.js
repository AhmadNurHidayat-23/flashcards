export async function request(url, method = 'GET', body) {
    let response;
    try {
        response = await fetch(url, {
            method,
            headers: { Accept: 'application/json', 'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            ...(body === undefined ? {} : { body: JSON.stringify(body) }),
        });
    } catch {
        throw new Error('Koneksi terputus. Periksa koneksi lalu coba lagi.');
    }
    if (!response.ok) {
        if (response.status === 419) throw new Error('Sesi berakhir. Muat ulang halaman untuk melanjutkan.');
        if (response.status === 409) throw new Error('Latihan sudah selesai. Buka hasil melalui riwayat kumpulan.');
        throw new Error('Perubahan belum tersimpan. Coba lagi atau muat ulang halaman.');
    }
    return response.json();
}
