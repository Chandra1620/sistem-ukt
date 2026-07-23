<?php

namespace App\Mail;

use App\Models\Pendaftaran;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DokumenDitolakMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Pendaftaran $pendaftaran,
        public string $alasan
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Dokumen Ditolak - ' . $this->pendaftaran->no_pendaftaran,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.dokumen-ditolak',
            with: [
                'nama'           => $this->pendaftaran->nama_lengkap,
                'noPendaftaran'  => $this->pendaftaran->no_pendaftaran,
                'alasan'         => $this->alasan,
                'url'            => url('/mahasiswa/pendaftaran/' . $this->pendaftaran->id),
            ],
        );
    }
}
