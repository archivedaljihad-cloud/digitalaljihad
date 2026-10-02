# -*- coding: utf-8 -*-
import os
import sys
from reportlab.lib.pagesizes import A4
from reportlab.lib import colors
from reportlab.lib.units import mm
from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle
from reportlab.platypus import (
    SimpleDocTemplate, Paragraph, Spacer, Table, TableStyle, PageBreak, KeepTogether, HRFlowable
)
from reportlab.pdfgen import canvas

class NumberedCanvas(canvas.Canvas):
    def __init__(self, *args, **kwargs):
        super(NumberedCanvas, self).__init__(*args, **kwargs)
        self._saved_page_states = []

    def showPage(self):
        self._saved_page_states.append(dict(self.__dict__))
        self._startPage()

    def save(self):
        num_pages = len(self._saved_page_states)
        for state in self._saved_page_states:
            self.__dict__.update(state)
            self.draw_page_decorations(num_pages)
            super(NumberedCanvas, self).showPage()
        super(NumberedCanvas, self).save()

    def draw_page_decorations(self, page_count):
        self.saveState()
        self.setFont("Helvetica-Bold", 8)
        self.setFillColor(colors.HexColor("#065F46"))
        
        # Header (halaman > 1)
        if self._pageNumber > 1:
            self.drawString(15 * mm, 285 * mm, "PANDUAN AKTIVASI KEMBALI AKUN RENDER & SWITCHER DISPLAY TV")
            self.drawRightString(195 * mm, 285 * mm, "MASJID JAMI' AL JIHAD")
            self.setStrokeColor(colors.HexColor("#CBD5E1"))
            self.setLineWidth(0.5)
            self.line(15 * mm, 282 * mm, 195 * mm, 282 * mm)
            
        # Footer
        self.setStrokeColor(colors.HexColor("#CBD5E1"))
        self.setLineWidth(0.5)
        self.line(15 * mm, 15 * mm, 195 * mm, 15 * mm)
        
        self.setFont("Helvetica", 8)
        self.setFillColor(colors.HexColor("#64748B"))
        footer_text = "Dokumen Panduan Teknis • Masjid Jami' Al Jihad • GitHub Pages Switcher"
        page_str = f"Halaman {self._pageNumber} dari {page_count}"
        self.drawString(15 * mm, 11 * mm, footer_text)
        self.drawRightString(195 * mm, 11 * mm, page_str)
        self.restoreState()

def generate_pdf(output_pdf):
    doc = SimpleDocTemplate(
        output_pdf,
        pagesize=A4,
        leftMargin=15 * mm,
        rightMargin=15 * mm,
        topMargin=16 * mm,
        bottomMargin=18 * mm
    )

    styles = getSampleStyleSheet()

    # Custom styles
    style_title = ParagraphStyle(
        'DocTitle',
        parent=styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=18,
        leading=22,
        textColor=colors.HexColor('#064E3B')
    )
    style_subtitle = ParagraphStyle(
        'DocSubtitle',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=10,
        leading=14,
        textColor=colors.HexColor('#047857')
    )
    style_h1 = ParagraphStyle(
        'Heading1_Custom',
        parent=styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=12,
        leading=16,
        textColor=colors.HexColor('#064E3B'),
        spaceBefore=12,
        spaceAfter=6
    )
    style_body = ParagraphStyle(
        'Body_Custom',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=9.5,
        leading=14,
        textColor=colors.HexColor('#1E293B')
    )
    style_body_bold = ParagraphStyle(
        'Body_Bold_Custom',
        parent=style_body,
        fontName='Helvetica-Bold'
    )
    style_callout = ParagraphStyle(
        'CalloutText',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=9,
        leading=13.5,
        textColor=colors.HexColor('#0F5132')
    )
    style_code = ParagraphStyle(
        'CodeStyle',
        parent=styles['Normal'],
        fontName='Courier-Bold',
        fontSize=8.5,
        leading=12,
        textColor=colors.HexColor('#0F172A')
    )
    style_th = ParagraphStyle(
        'TableHeader',
        parent=styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=9,
        leading=12,
        textColor=colors.HexColor('#FFFFFF')
    )
    style_td = ParagraphStyle(
        'TableCell',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=8.5,
        leading=12,
        textColor=colors.HexColor('#1E293B')
    )

    story = []

    # === HEADER UTAMA ===
    header_data = [
        [
            Paragraph("<b>PANDUAN TEKNIS AKTIVASI KEMBALI AKUN RENDER</b>", style_title),
            Paragraph("<b>STATUS: PANDUAN RESMI</b><br/>Display TV Masjid Al-Jihad", ParagraphStyle(
                'Badge', parent=styles['Normal'], fontName='Helvetica-Bold', fontSize=8, leading=11,
                alignment=2, textColor=colors.HexColor('#B45309')
            ))
        ]
    ]
    t_header = Table(header_data, colWidths=[125 * mm, 55 * mm])
    t_header.setStyle(TableStyle([
        ('VALIGN', (0, 0), (-1, -1), 'TOP'),
        ('BOTTOMPADDING', (0, 0), (-1, -1), 0),
        ('TOPPADDING', (0, 0), (-1, -1), 0),
    ]))
    story.append(t_header)
    story.append(Spacer(1, 2 * mm))
    story.append(Paragraph("Langkah Demi Langkah Mengaktifkan Kembali Layar Display TV dari Mode Perbaikan ke Dua Akun Render", style_subtitle))
    story.append(Spacer(1, 3 * mm))
    story.append(HRFlowable(width="100%", thickness=1.5, color=colors.HexColor("#059669"), spaceBefore=1, spaceAfter=8))

    # === KOTAK INFORMASI AKUN & URL ===
    story.append(Paragraph("1. Data Konfigurasi Akun & Layanan", style_h1))
    
    info_table_data = [
        [Paragraph("Komponen", style_th), Paragraph("Identitas / URL Resmi", style_th), Paragraph("Keterangan Jadwal", style_th)],
        [
            Paragraph("<b>Akun GitHub</b>", style_td),
            Paragraph("<b>mydowndrive-ops</b> (Email: <i>my.downdrive@gmail.com</i>)", style_td),
            Paragraph("Akun pengelola repositori", style_td)
        ],
        [
            Paragraph("<b>Repositori Switcher</b>", style_td),
            Paragraph("https://github.com/mydowndrive-ops/aljihad", style_td),
            Paragraph("Tempat file <code>index.html</code>", style_td)
        ],
        [
            Paragraph("<b>URL Display TV (Pages)</b>", style_td),
            Paragraph("<b>https://mydowndrive-ops.github.io/aljihad/</b>", style_td),
            Paragraph("Link yang dipasang di Smart TV Masjid", style_td)
        ],
        [
            Paragraph("<b>Render Server 1</b>", style_td),
            Paragraph("https://digitalaljihad1.onrender.com", style_td),
            Paragraph("<b>Tanggal 1 s/d 15</b> setiap bulan", style_td)
        ],
        [
            Paragraph("<b>Render Server 2</b>", style_td),
            Paragraph("https://digital-aljihad.onrender.com", style_td),
            Paragraph("<b>Tanggal 16 s/d 31</b> setiap bulan", style_td)
        ]
    ]
    t_info = Table(info_table_data, colWidths=[40 * mm, 85 * mm, 55 * mm])
    t_info.setStyle(TableStyle([
        ('BACKGROUND', (0, 0), (-1, 0), colors.HexColor('#065F46')),
        ('TEXTCOLOR', (0, 0), (-1, 0), colors.white),
        ('GRID', (0, 0), (-1, -1), 0.5, colors.HexColor('#CBD5E1')),
        ('VALIGN', (0, 0), (-1, -1), 'MIDDLE'),
        ('TOPPADDING', (0, 0), (-1, -1), 4),
        ('BOTTOMPADDING', (0, 0), (-1, -1), 4),
        ('ROWBACKGROUNDS', (0, 1), (-1, -1), [colors.HexColor('#F8FAFC'), colors.white])
    ]))
    story.append(t_info)
    story.append(Spacer(1, 5 * mm))

    # === LANGKAH-LANGKAH PENGEMBALIAN ===
    story.append(Paragraph("2. Prosedur Aktivasi Kembali (Saat Akun Render Sudah Siap)", style_h1))
    
    steps = [
        ("LANGKAH 1", "Unsuspend / Resume Web Service di Dashboard Render", [
            "Buka dashboard Render di browser: <b>https://dashboard.render.com</b>.",
            "Login menggunakan akun <b>mydowndrive-ops</b> (atau email <b>my.downdrive@gmail.com</b>).",
            "Buka service pertama: <b>digitalaljihad1</b>. Masuk ke tab <b>Settings</b>, gulir ke paling bawah, lalu klik tombol <b>Resume Service / Unsuspend</b>.",
            "Lakukan hal yang sama untuk service kedua: <b>digital-aljihad</b>.",
            "Pastikan kedua service telah berubah statusnya menjadi bertanda hijau <b>✓ Live</b>."
        ]),
        ("LANGKAH 2", "Mengubah Saklar MODE_PERBAIKAN di GitHub", [
            "Buka repositori GitHub: <b>https://github.com/mydowndrive-ops/aljihad</b>.",
            "Klik pada berkas <b>index.html</b>, lalu klik ikon pensil (<b>Edit this file</b>).",
            "Cari baris kode saklar utama (sekitar baris ke-201):<br/>"
            "&nbsp;&nbsp;&nbsp;&nbsp;<code>const MODE_PERBAIKAN = true;</code>",
            "Ubah kata <b>true</b> menjadi <b>false</b> sehingga menjadi:<br/>"
            "&nbsp;&nbsp;&nbsp;&nbsp;<font color='#065F46'><b>const MODE_PERBAIKAN = false;</b></font>",
            "Klik tombol hijau <b>Commit changes...</b> di pojok kanan atas untuk menyimpan."
        ]),
        ("LANGKAH 3", "Verifikasi Tampilan di Layar TV / Browser", [
            "Buka alamat: <b>https://mydowndrive-ops.github.io/aljihad/</b> di browser laptop atau TV.",
            "Layar akan menampilkan logo Masjid Al-Jihad dengan animasi putar (splash screen).",
            "Setelah 5–15 detik (waktu bangun server Render), tampilan akan otomatis terbuka dan menyiarkan display TV digital jadwal sholat & pengumuman secara normal."
        ])
    ]

    for step_num, step_title, step_points in steps:
        box_data = [
            [
                Paragraph(f"<b>{step_num}: {step_title}</b>", ParagraphStyle(
                    'StepH', parent=styles['Normal'], fontName='Helvetica-Bold', fontSize=10, leading=14,
                    textColor=colors.HexColor('#064E3B')
                ))
            ]
        ]
        for p in step_points:
            box_data.append([Paragraph(f"• {p}", style_body)])

        t_box = Table(box_data, colWidths=[180 * mm])
        t_box.setStyle(TableStyle([
            ('BACKGROUND', (0, 0), (-1, -1), colors.HexColor('#F0FDF4')),
            ('BOX', (0, 0), (-1, -1), 1, colors.HexColor('#86EFAC')),
            ('TOPPADDING', (0, 0), (-1, 0), 6),
            ('BOTTOMPADDING', (0, 0), (-1, 0), 2),
            ('TOPPADDING', (0, 1), (-1, -1), 2),
            ('BOTTOMPADDING', (0, -1), (-1, -1), 6),
            ('LEFTPADDING', (0, 0), (-1, -1), 10),
            ('RIGHTPADDING', (0, 0), (-1, -1), 10),
        ]))
        story.append(t_box)
        story.append(Spacer(1, 3.5 * mm))

    story.append(PageBreak())

    # === HALAMAN 2: DETAIL SAKLAR & KODE RESMI ===
    story.append(Paragraph("3. Detail Perubahan Kode (index.html)", style_h1))
    story.append(Paragraph(
        "Berikut adalah perbedaan kode saat mode perbaikan aktif dibandingkan saat dikembalikan ke normal:",
        style_body
    ))
    story.append(Spacer(1, 2 * mm))

    diff_data = [
        [Paragraph("Mode Saat Ini (Sedang Perbaikan)", style_th), Paragraph("Mode Normal (Kedua Render Aktif)", style_th)],
        [
            Paragraph("<font color='#DC2626'><b>// TAMPILKAN LAYAR PERBAIKAN</b></font><br/><code>const MODE_PERBAIKAN = <b>true</b>;</code><br/><br/><i>Efek: Splash screen memudar ke layar pemberitahuan pemeliharaan & jam digital. Iframe dinonaktifkan.</i>", style_td),
            Paragraph("<font color='#059669'><b>// JALANKAN SWITCHER NORMAL</b></font><br/><code>const MODE_PERBAIKAN = <b>false</b>;</code><br/><br/><i>Efek: Iframe langsung memuat Render 1 (tgl 1-15) atau Render 2 (tgl 16-31) sesuai tanggal hari ini.</i>", style_td)
        ]
    ]
    t_diff = Table(diff_data, colWidths=[90 * mm, 90 * mm])
    t_diff.setStyle(TableStyle([
        ('BACKGROUND', (0, 0), (0, 0), colors.HexColor('#991B1B')),
        ('BACKGROUND', (1, 0), (1, 0), colors.HexColor('#065F46')),
        ('GRID', (0, 0), (-1, -1), 0.5, colors.HexColor('#CBD5E1')),
        ('VALIGN', (0, 0), (-1, -1), 'TOP'),
        ('TOPPADDING', (0, 0), (-1, -1), 6),
        ('BOTTOMPADDING', (0, 0), (-1, -1), 6),
        ('ROWBACKGROUNDS', (0, 1), (-1, -1), [colors.HexColor('#FFF1F2'), colors.HexColor('#F0FDF4')])
    ]))
    story.append(t_diff)
    story.append(Spacer(1, 6 * mm))

    # === FITUR PINTAR SWITCHER ===
    story.append(Paragraph("4. Fitur Pintar Bawaan Switcher (Otomasi Penuh 24 Jam)", style_h1))
    
    features = [
        ("Otomasi Switch Tanggal 15 ke 16 (00:00 WIB)", 
         "Skrip memonitor tanggal setiap 60 detik. Saat pergantian tanggal dari 15 ke 16 tengah malam tiba, halaman TV otomatis me-refresh dirinya sendiri dan beralih ke Server 2 tanpa perlu campur tangan manusia."),
        ("Pembersihan Memori Harian (03:15 WIB)", 
         "Setiap pukul 03:15 dini hari (waktu sepi sebelum adzan Subuh), sistem melakukan soft reload otomatis untuk membersihkan cache RAM Smart TV/STB agar tidak pernah nge-lag atau freeze."),
        ("Penahan Error Logo Gambar (Anti Freeze)", 
         "Kode telah dilengkapi pengaman <code>this.onerror = null;</code> sehingga jika jaringan mengalami kendala sementara, browser tidak akan mengalami infinite-loop yang menyebabkan layar blank hitam.")
    ]

    feat_data = []
    for f_title, f_desc in features:
        feat_data.append([
            Paragraph(f"<b>✓ {f_title}</b><br/>{f_desc}", style_td)
        ])
    t_feat = Table(feat_data, colWidths=[180 * mm])
    t_feat.setStyle(TableStyle([
        ('BACKGROUND', (0, 0), (-1, -1), colors.HexColor('#F8FAFC')),
        ('BOX', (0, 0), (-1, -1), 0.5, colors.HexColor('#CBD5E1')),
        ('INNERGRID', (0, 0), (-1, -1), 0.5, colors.HexColor('#E2E8F0')),
        ('TOPPADDING', (0, 0), (-1, -1), 5),
        ('BOTTOMPADDING', (0, 0), (-1, -1), 5),
        ('LEFTPADDING', (0, 0), (-1, -1), 8),
        ('RIGHTPADDING', (0, 0), (-1, -1), 8)
    ]))
    story.append(t_feat)
    story.append(Spacer(1, 8 * mm))

    # === CATATAN AKHIR / TTD ===
    note_box = [
        [
            Paragraph(
                "<b>Catatan Penting Pengurus:</b> Simpan dokumen PDF ini. Kapan pun Anda ingin mengembalikan tampilan TV ke siaran online dari kedua akun Render, ikuti 3 langkah di Halaman 1. Sistem dirancang ramah dan mudah dikelola bahkan dari ponsel.",
                style_callout
            )
        ]
    ]
    t_note = Table(note_box, colWidths=[180 * mm])
    t_note.setStyle(TableStyle([
        ('BACKGROUND', (0, 0), (-1, -1), colors.HexColor('#FEF3C7')),
        ('BOX', (0, 0), (-1, -1), 1, colors.HexColor('#F59E0B')),
        ('TOPPADDING', (0, 0), (-1, -1), 6),
        ('BOTTOMPADDING', (0, 0), (-1, -1), 6),
        ('LEFTPADDING', (0, 0), (-1, -1), 10),
        ('RIGHTPADDING', (0, 0), (-1, -1), 10),
    ]))
    story.append(t_note)

    doc.build(story, canvasmaker=NumberedCanvas)
    print("PDF berhasil dibuat!")

if __name__ == '__main__':
    out_path = os.path.abspath(r"c:\Users\anthu\Documents\【Project】\DIGITALv304\PANDUAN_MENGEMBALIKAN_AKUN_RENDER.pdf")
    generate_pdf(out_path)
