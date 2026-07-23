@component('mail::message')
    # Hasil UKT Anda Telah Keluar

    <div style="text-align: center; margin-bottom: 24px;">
        <div
            style="background: #d1fae5; width: 60px; height: 60px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px;">
            <span style="font-size: 2rem;">🎉</span>
        </div>
    </div>

    ## Halo **{{ $nama }}**,

    Kami informasikan bahwa hasil perhitungan UKT untuk pendaftaran Anda dengan nomor **{{ $noPendaftaran }}** telah
    **selesai diproses** dan dapat Anda lihat sekarang.

    ---

    ### 📊 **Ringkasan Hasil UKT**

    <div
        style="background: linear-gradient(135deg, #1e3a5f 0%, #2d6a9f 100%); border-radius: 20px; padding: 24px; margin: 20px 0; text-align: center;">
        <div
            style="color: rgba(255,255,255,0.7); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 8px;">
            🏆 Golongan UKT
        </div>
        <div style="color: #ffd700; font-size: 3rem; font-weight: 800; line-height: 1; margin-bottom: 12px;">
            {{ $golongan }}
        </div>
        <div style="color: white; font-size: 1.1rem; font-weight: 600; margin-bottom: 16px;">
            {{ $nominal }}
        </div>
        <div style="border-top: 1px solid rgba(255,255,255,0.2); padding-top: 12px; margin-top: 8px;">
            <div style="color: rgba(255,255,255,0.6); font-size: 0.7rem;">
                Nominal UKT per semester
            </div>
        </div>
    </div>

    ---

    ### 📋 **Detail Informasi**

    <div style="background: #f8fafc; border-radius: 16px; padding: 16px; margin: 16px 0;">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 8px 0; color: #64748b; width: 40%;">📝 No. Pendaftaran</td>
                <td style="padding: 8px 0; font-weight: 600; color: #1e293b;"><strong>{{ $noPendaftaran }}</strong></td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: #64748b;">🎯 Golongan UKT</td>
                <td style="padding: 8px 0; font-weight: 600; color: #1e293b;">Golongan <strong>{{ $golongan }}</strong>
                </td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: #64748b;">💰 Nominal UKT</td>
                <td style="padding: 8px 0; font-weight: 600; color: #10b981;">{{ $nominal }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: #64748b;">📅 Tanggal Keluar</td>
                <td style="padding: 8px 0; font-weight: 600; color: #1e293b;">{{ now()->translatedFormat('d F Y H:i') }} WIB
                </td>
            </tr>
        </table>
    </div>

    ---

    ### ✅ **Yang Dapat Anda Lakukan**

    <div style="margin: 16px 0;">
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
            <div
                style="background: #eef2ff; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <span style="font-size: 0.9rem;">👁️</span>
            </div>
            <div style="flex: 1;">
                <strong>Lihat Detail Lengkap</strong>
                <div style="font-size: 0.75rem; color: #64748b;">Cek detail perhitungan SAW dan informasi lainnya</div>
            </div>
        </div>
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
            <div
                style="background: #d1fae5; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <span style="font-size: 0.9rem;">📥</span>
            </div>
            <div style="flex: 1;">
                <strong>Cetak / Unduh Hasil</strong>
                <div style="font-size: 0.75rem; color: #64748b;">Simpan bukti hasil UKT untuk arsip</div>
            </div>
        </div>
        <div style="display: flex; align-items: center; gap: 12px;">
            <div
                style="background: #fee2e2; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <span style="font-size: 0.9rem;">⚖️</span>
            </div>
            <div style="flex: 1;">
                <strong>Ajukan Banding (Opsional)</strong>
                <div style="font-size: 0.75rem; color: #64748b;">Jika hasil tidak sesuai, Anda dapat mengajukan banding
                </div>
            </div>
        </div>
    </div>

    ---

    ### 🔗 **Akses Cepat**

    @component('mail::button', ['url' => $url, 'color' => 'green'])
        🎯 Lihat Hasil UKT Saya
    @endcomponent

    ---

    ### ⚠️ **Informasi Penting**

    <div style="background: #fef3c7; border-radius: 12px; padding: 14px; margin: 20px 0;">
        <div style="display: flex; gap: 10px; align-items: flex-start;">
            <span style="font-size: 1.2rem;">⚠️</span>
            <div style="flex: 1; font-size: 0.8rem; color: #92400e;">
                <strong>Catatan Penting:</strong><br>
                - Hasil UKT bersifat final setelah masa banding berakhir<br>
                - Pembayaran UKT dapat dilakukan sesuai jadwal yang ditentukan<br>
                - Jika ada pertanyaan, segera hubungi panitia UKT
            </div>
        </div>
    </div>

    ---

    <div style="margin-top: 24px; background: #f8fafc; padding: 16px; border-radius: 12px;">
        <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap; justify-content: center;">
            <div style="text-align: center;">
                <div style="font-size: 0.7rem; color: #94a3b8;">📞 Butuh Bantuan?</div>
                <div style="font-weight: 600; font-size: 0.8rem;">(021) 1234-5678</div>
            </div>
            <div style="width: 1px; height: 30px; background: #e2e8f0;"></div>
            <div style="text-align: center;">
                <div style="font-size: 0.7rem; color: #94a3b8;">✉️ Email Support</div>
                <div style="font-weight: 600; font-size: 0.8rem;">admin@ukt.ac.id</div>
            </div>
            <div style="width: 1px; height: 30px; background: #e2e8f0;"></div>
            <div style="text-align: center;">
                <div style="font-size: 0.7rem; color: #94a3b8;">🕒 Jam Kerja</div>
                <div style="font-weight: 600; font-size: 0.8rem;">Senin - Jumat, 08:00 - 16:00</div>
            </div>
        </div>
    </div>

    ---

    Terima kasih atas partisipasi Anda dalam seleksi UKT.

    **Sistem UKT**
    *Sistem Pendukung Keputusan UKT*

    @slot('subcopy')
        Jika Anda mengalami kendala saat mengakses tombol di atas, silakan salin dan buka link berikut di browser Anda:
        [{{ $url }}]({{ $url }})

        ---

        **Masa Banding:** Jika Anda tidak setuju dengan hasil ini, Anda dapat mengajukan banding dalam waktu 7 hari kerja
        setelah email ini diterima.
    @endslot
@endcomponent
