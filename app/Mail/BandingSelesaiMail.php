<?php

namespace App\Mail;

use App\Models\Pendaftaran;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BandingSelesaiMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Pendaftaran $pendaftaran,
        public string $keputusan,
        public ?int $golonganBaru = null
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Hasil Banding UKT - ' . $this->pendaftaran->no_pendaftaran,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.banding-selesai',
            with: [
                'nama'          => $this->pendaftaran->nama_lengkap,
                'noPendaftaran' => $this->pendaftaran->no_pendaftaran,
                'keputusan'     => $this->keputusan,
                'golonganBaru'  => $this->golonganBaru,
                'url'           => url('/mahasiswa/hasil-ukt/' . $this->pendaftaran->id),
            ],
        );
    }
}
