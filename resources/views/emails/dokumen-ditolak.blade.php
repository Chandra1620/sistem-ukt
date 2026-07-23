@component('mail::message')
    # Pengajuan Dokumen Ditolak

    <div style="text-align: center; margin-bottom: 24px;">
        <div
            style="background: #fee2e2; width: 60px; height: 60px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px;">
            <span style="font-size: 2rem;">📄</span>
        </div>
    </div>

    ## Halo **{{ $nama }}**,

    Kami informasikan bahwa dokumen pendaftaran UKT Anda dengan nomor pendaftaran **{{ $noPendaftaran }}** telah **ditolak**
    oleh reviewer.

    ---

    ### 📋 **Alasan Penolakan**

    <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 16px; border-radius: 12px; margin: 16px 0;">
        <div style="font-weight: 600; color: #92400e; margin-bottom: 8px;">
            <span style="font-size: 1.1rem;">⚠️</span> Catatan Reviewer:
        </div>
        <div style="color: #78350f; font-size: 0.95rem; line-height: 1.5;">
            {{ $alasan }}
        </div>
    </div>

    ---

    ### ✅ **Yang Perlu Anda Lakukan**

    1. **Login** ke Sistem UKT
    2. **Periksa** kembali dokumen yang diperlukan
    3. **Upload ulang** dokumen yang sesuai dengan persyaratan
    4. **Submit kembali** pendaftaran Anda

    > **Penting:** Pastikan dokumen yang diupload jelas, lengkap, dan sesuai dengan ketentuan yang berlaku.

    ---

    ### 🔗 **Akses Cepat**

    @component('mail::button', ['url' => $url, 'color' => 'error'])
        🎯 Periksa Pendaftaran Saya
    @endcomponent

    ---

    <div style="margin-top: 24px; padding-top: 16px; border-top: 1px solid #e2e8f0;">
        <div style="font-size: 0.85rem; color: #64748b;">
            <strong>💡 Tips Upload Dokumen:</strong>
            <ul style="margin-top: 8px; padding-left: 20px;">
                <li>Pastikan file berformat PDF, JPG, atau PNG</li>
                <li>Ukuran maksimal 2MB per file</li>
                <li>Dokumen harus jelas dan terbaca</li>
                <li>Hindari foto yang buram atau terpotong</li>
            </ul>
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

    Terima kasih atas perhatian dan kerjasamanya.

    **Sistem UKT**
    *Sistem Pendukung Keputusan UKT*

    @slot('subcopy')
        Jika Anda mengalami kendala saat mengakses tombol di atas, silakan salin dan buka link berikut di browser Anda:
        [{{ $url }}]({{ $url }})
    @endslot
@endcomponent
