<?php

namespace App\Mail;

use App\Models\Pendaftaran;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class HasilUktMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Pendaftaran $pendaftaran,
        public int $golongan,
        public float $nominal
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Hasil UKT Anda - ' . $this->pendaftaran->no_pendaftaran,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.hasil-ukt',
            with: [
                'nama'          => $this->pendaftaran->nama_lengkap,
                'noPendaftaran' => $this->pendaftaran->no_pendaftaran,
                'golongan'      => $this->golongan,
                'nominal'       => 'Rp ' . number_format($this->nominal, 0, ',', '.'),
                'url'           => url('/mahasiswa/hasil-ukt/' . $this->pendaftaran->id),
            ],
        );
    }
}
