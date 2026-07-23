@component('mail::message')
    # Hasil Banding UKT

    <div style="text-align: center; margin-bottom: 24px;">
        <div
            style="background: {{ $keputusan === 'diterima' ? '#d1fae5' : '#fee2e2' }}; width: 60px; height: 60px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px;">
            <span style="font-size: 2rem;">{{ $keputusan === 'diterima' ? '✅' : '❌' }}</span>
        </div>
    </div>

    ## Halo **{{ $nama }}**,

    Kami informasikan bahwa pengajuan banding Anda untuk pendaftaran dengan nomor **{{ $noPendaftaran }}** telah **selesai
    diproses** oleh panitia.

    ---

    ### 📋 **Hasil Keputusan Banding**

    <div
        style="background: {{ $keputusan === 'diterima' ? '#d1fae5' : '#fee2e2' }}; border-radius: 20px; padding: 24px; margin: 20px 0; text-align: center;">
        <div
            style="color: {{ $keputusan === 'diterima' ? '#065f46' : '#991b1b' }}; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 8px;">
            ⚖️ Keputusan Banding
        </div>
        <div
            style="color: {{ $keputusan === 'diterima' ? '#10b981' : '#dc2626' }}; font-size: 2rem; font-weight: 800; line-height: 1; margin-bottom: 16px;">
            {{ $keputusan === 'diterima' ? 'DITERIMA' : 'DITOLAK' }}
        </div>
        @if ($golonganBaru)
            <div style="border-top: 1px solid rgba(0,0,0,0.1); padding-top: 16px; margin-top: 8px;">
                <div
                    style="color: {{ $keputusan === 'diterima' ? '#065f46' : '#991b1b' }}; font-size: 0.8rem; margin-bottom: 4px;">
                    🎯 Golongan Baru
                </div>
                <div style="font-size: 1.8rem; font-weight: 800; color: #1e3a5f;">
                    {{ $golonganBaru }}
                </div>
            </div>
        @endif
    </div>

    ---

    ### 📊 **Perbandingan Hasil UKT**

    <div style="background: #f8fafc; border-radius: 16px; padding: 16px; margin: 16px 0;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align: left; padding: 10px 0; color: #64748b; font-size: 0.75rem;">&nbsp;</th>
                    <th style="text-align: center; padding: 10px 0; color: #64748b; font-size: 0.75rem;">Sebelum Banding
                    </th>
                    <th style="text-align: center; padding: 10px 0; color: #64748b; font-size: 0.75rem;">Setelah Banding
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding: 10px 0; font-weight: 600;">Golongan UKT</td>
                    <td style="text-align: center; padding: 10px 0;">
                        <span
                            style="background: #e2e8f0; padding: 4px 12px; border-radius: 40px; font-size: 0.8rem; font-weight: 600;">{{ $golonganLama ?? '-' }}</span>
                    </td>
                    <td style="text-align: center; padding: 10px 0;">
                        <span
                            style="background: {{ $keputusan === 'diterima' ? '#d1fae5' : '#fee2e2' }}; padding: 4px 12px; border-radius: 40px; font-size: 0.8rem; font-weight: 600; color: {{ $keputusan === 'diterima' ? '#065f46' : '#991b1b' }};">
                            {{ $golonganBaru ?? ($golonganLama ?? '-') }}
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    ---

    @if ($keputusan === 'diterima')
        ### ✅ **Yang Perlu Anda Lakukan**

        <div style="margin: 16px 0;">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                <div
                    style="background: #d1fae5; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <span style="font-size: 0.9rem;">💰</span>
                </div>
                <div style="flex: 1;">
                    <strong>Perhatikan Nominal Baru</strong>
                    <div style="font-size: 0.75rem; color: #64748b;">UKT Anda telah berubah sesuai golongan baru</div>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                <div
                    style="background: #eef2ff; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <span style="font-size: 0.9rem;">👁️</span>
                </div>
                <div style="flex: 1;">
                    <strong>Cek Hasil Terbaru</strong>
                    <div style="font-size: 0.75rem; color: #64748b;">Lihat detail hasil UKT yang telah diperbarui</div>
                </div>
            </div>
        </div>
    @else
        ### 📋 **Yang Perlu Anda Ketahui**

        <div style="margin: 16px 0;">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                <div
                    style="background: #fee2e2; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <span style="font-size: 0.9rem;">📌</span>
                </div>
                <div style="flex: 1;">
                    <strong>Keputusan Bersifat Final</strong>
                    <div style="font-size: 0.75rem; color: #64748b;">Keputusan banding tidak dapat diajukan kembali</div>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 12px;">
                <div
                    style="background: #eef2ff; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <span style="font-size: 0.9rem;">💰</span>
                </div>
                <div style="flex: 1;">
                    <strong>Golongan Tetap</strong>
                    <div style="font-size: 0.75rem; color: #64748b;">Golongan UKT Anda tetap sesuai keputusan awal</div>
                </div>
            </div>
        </div>
    @endif

    ---

    ### 🔗 **Akses Cepat**

    @component('mail::button', ['url' => $url, 'color' => $keputusan === 'diterima' ? 'green' : 'blue'])
        🎯 Lihat Detail Hasil UKT
    @endcomponent

    ---

    ### ⚠️ **Informasi Penting**

    <div style="background: #fef3c7; border-radius: 12px; padding: 14px; margin: 20px 0;">
        <div style="display: flex; gap: 10px; align-items: flex-start;">
            <span style="font-size: 1.2rem;">⚠️</span>
            <div style="flex: 1; font-size: 0.8rem; color: #92400e;">
                <strong>Catatan Penting:</strong><br>
                - Keputusan banding bersifat final dan mengikat<br>
                - Perubahan golongan akan langsung diterapkan di sistem<br>
                - Silakan lakukan pembayaran sesuai ketentuan yang berlaku
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

    Terima kasih atas kepercayaan Anda kepada Sistem UKT.

    **Sistem UKT**
    *Sistem Pendukung Keputusan UKT*

    @slot('subcopy')
        Jika Anda mengalami kendala saat mengakses tombol di atas, silakan salin dan buka link berikut di browser Anda:
        [{{ $url }}]({{ $url }})

        @if ($keputusan === 'diterima')
            ---
            **Informasi Tambahan:** Perubahan golongan akan terlihat setelah Anda login ke sistem.
        @endif
    @endslot
@endcomponent
