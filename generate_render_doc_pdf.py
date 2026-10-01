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
        self.setFont("Helvetica", 8)
        self.setFillColor(colors.HexColor("#64748B"))
        
        # Header (pages > 1)
        if self._pageNumber > 1:
            self.drawString(15 * mm, 285 * mm, "Dokumentasi & Diagnostik Akun Render - Masjid Jami' Al-Jihad")
            self.setStrokeColor(colors.HexColor("#E2E8F0"))
            self.setLineWidth(0.5)
            self.line(15 * mm, 283 * mm, 195 * mm, 283 * mm)
            
        # Footer
        self.setStrokeColor(colors.HexColor("#E2E8F0"))
        self.setLineWidth(0.5)
        self.line(15 * mm, 15 * mm, 195 * mm, 15 * mm)
        
        footer_text = "Dokumen Panduan Teknis • Sistem Informasi Digital Masjid Jami' Al-Jihad"
        page_str = f"Halaman {self._pageNumber} dari {page_count}"
        self.drawString(15 * mm, 11 * mm, footer_text)
        self.drawRightString(195 * mm, 11 * mm, page_str)
        self.restoreState()

def build_pdf(filename):
    doc = SimpleDocTemplate(
        filename,
        pagesize=A4,
        leftMargin=15 * mm,
        rightMargin=15 * mm,
        topMargin=16 * mm,
        bottomMargin=18 * mm
    )

    styles = getSampleStyleSheet()

    # Custom styles
    primary_color = colors.HexColor("#0F766E")
    dark_color = colors.HexColor("#0F172A")
    text_color = colors.HexColor("#1E293B")
    muted_color = colors.HexColor("#64748B")

    style_title = ParagraphStyle(
        'DocTitle',
        parent=styles['Heading1'],
        fontName='Helvetica-Bold',
        fontSize=15,
        leading=18,
        textColor=primary_color,
        spaceAfter=3
    )

    style_subtitle = ParagraphStyle(
        'DocSubtitle',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=9,
        leading=12,
        textColor=muted_color,
        spaceAfter=10
    )

    style_h2 = ParagraphStyle(
        'Heading2Custom',
        parent=styles['Heading2'],
        fontName='Helvetica-Bold',
        fontSize=11,
        leading=14,
        textColor=dark_color,
        spaceBefore=10,
        spaceAfter=6
    )

    style_h3 = ParagraphStyle(
        'Heading3Custom',
        parent=styles['Heading3'],
        fontName='Helvetica-Bold',
        fontSize=9.5,
        leading=13,
        textColor=primary_color,
        spaceBefore=6,
        spaceAfter=3
    )

    style_body = ParagraphStyle(
        'BodyCustom',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=8.5,
        leading=12,
        textColor=text_color,
        spaceAfter=5
    )

    style_body_bold = ParagraphStyle(
        'BodyBoldCustom',
        parent=style_body,
        fontName='Helvetica-Bold'
    )

    style_card = ParagraphStyle(
        'CardText',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=8.5,
        leading=12.5,
        textColor=text_color
    )

    style_table_header = ParagraphStyle(
        'TableHeader',
        parent=styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=8,
        leading=10,
        textColor=colors.HexColor("#0F172A")
    )

    style_table_cell = ParagraphStyle(
        'TableCell',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=8,
        leading=10.5,
        textColor=text_color
    )

    style_code = ParagraphStyle(
        'CodeStyle',
        parent=styles['Normal'],
        fontName='Courier',
        fontSize=8,
        leading=10,
        textColor=colors.HexColor("#0F766E")
    )

    story = []

    # Title & Header
    header_table_data = [
        [
            Paragraph("DOKUMENTASI & PANDUAN AKUN RENDER", style_title),
            Paragraph("<b>Tanggal:</b> 1 Oktober 2026<br/><b>Klasifikasi:</b> Arsip & Panduan", style_table_cell)
        ],
        [
            Paragraph("Aplikasi Display Digital Masjid Jami' Al-Jihad • Status & Manajemen Web Service", style_subtitle),
            Paragraph("<b>Status:</b> Selesai Ditinjau", style_table_cell)
        ]
    ]
    t_header = Table(header_table_data, colWidths=[125 * mm, 55 * mm])
    t_header.setStyle(TableStyle([
        ('VALIGN', (0,0), (-1,-1), 'TOP'),
        ('ALIGN', (1,0), (1,-1), 'RIGHT'),
        ('BOTTOMPADDING', (0,0), (-1,-1), 0),
        ('TOPPADDING', (0,0), (-1,-1), 0),
        ('LEFTPADDING', (0,0), (-1,-1), 0),
        ('RIGHTPADDING', (0,0), (-1,-1), 0),
    ]))
    story.append(t_header)
    story.append(HRFlowable(width="100%", thickness=1.5, color=primary_color, spaceAfter=8))

    # Ringkasan Jawaban Utama (Callout Box)
    summary_html = (
        "<b>📌 JAWABAN UTAMA: ASAL AKUN URL https://digital-aljihad.onrender.com/</b><br/>"
        "URL tersebut berasal dari akun Render yang terhubung dengan kredensial berikut:<br/>"
        "• <b>Akun Login / GitHub:</b> <font color='#0F766E'><b>mydowndrive-ops</b></font><br/>"
        "• <b>Email Terdaftar:</b> <font color='#0F766E'><b>my.downdrive@gmail.com</b></font><br/>"
        "• <b>Repositori GitHub Asal:</b> <font color='#0F766E'><b>mydowndrive-ops/digitalaljihad001</b></font><br/>"
        "• <b>Nama Web Service di Render:</b> <font color='#0F766E'><b>digital.aljihad</b></font><br/>"
        "• <b>Render Service ID:</b> <font color='#0F766E'><b>srv-daffllou01pc73a98acg</b></font><br/>"
        "• <b>Database Cloud Asal:</b> TiDB Cloud Serverless (gateway01.ap-southeast-1.prod.aws.tidbcloud.com)"
    )
    t_box = Table([[Paragraph(summary_html, style_card)]], colWidths=[180 * mm])
    t_box.setStyle(TableStyle([
        ('BACKGROUND', (0, 0), (-1, -1), colors.HexColor("#F0FDFA")),
        ('BOX', (0, 0), (-1, -1), 1, colors.HexColor("#99F6E4")),
        ('TOPPADDING', (0, 0), (-1, -1), 6),
        ('BOTTOMPADDING', (0, 0), (-1, -1), 6),
        ('LEFTPADDING', (0, 0), (-1, -1), 8),
        ('RIGHTPADDING', (0, 0), (-1, -1), 8),
    ]))
    story.append(t_box)
    story.append(Spacer(1, 8))

    # Bagian 1: Hasil Diagnostik
    story.append(Paragraph("1. Hasil Diagnostik Pengecekan Server Langsung (Live Network Check)", style_h2))
    story.append(Paragraph(
        "Pemeriksaan dilakukan secara langsung dari terminal ke router CDN server Render untuk memastikan status tiap subdomain:",
        style_body
    ))

    status_data = [
        [
            Paragraph("Subdomain URL", style_table_header),
            Paragraph("Status HTTP", style_table_header),
            Paragraph("Header Respons Render", style_table_header),
            Paragraph("Keterangan Status", style_table_header)
        ],
        [
            Paragraph("<b>digitalaljihad001.onrender.com</b>", style_table_cell),
            Paragraph("<font color='#DC2626'><b>503 Service Unavailable</b></font>", style_table_cell),
            Paragraph("x-render-routing: suspend-by-user", style_code),
            Paragraph("<b>SUDAH NONAKTIF</b> (Berhasil disuspend oleh Anda).", style_table_cell)
        ],
        [
            Paragraph("<b>digital-aljihad1.onrender.com</b>", style_table_cell),
            Paragraph("<font color='#64748B'><b>404 Not Found</b></font>", style_table_cell),
            Paragraph("x-render-routing: no-server", style_code),
            Paragraph("Tidak aktif / telah dihapus.", style_table_cell)
        ],
        [
            Paragraph("<b>digitalaljihad.onrender.com</b>", style_table_cell),
            Paragraph("<font color='#64748B'><b>404 Not Found</b></font>", style_table_cell),
            Paragraph("x-render-routing: no-server", style_code),
            Paragraph("Nama tanpa tanda strip ini tidak terhubung.", style_table_cell)
        ],
        [
            Paragraph("<b>digital-aljihad.onrender.com</b>", style_table_cell),
            Paragraph("<font color='#16A34A'><b>200 OK (MASIH LIVE)</b></font>", style_table_cell),
            Paragraph("x-render-origin-server: nginx<br/>rndr-id: 9fb7f507-d7f0", style_code),
            Paragraph("<b>MASIH BERJALAN</b> & belum berstatus suspended.", style_table_cell)
        ]
    ]

    t_status = Table(status_data, colWidths=[52 * mm, 38 * mm, 45 * mm, 45 * mm])
    t_status.setStyle(TableStyle([
        ('BACKGROUND', (0, 0), (-1, 0), colors.HexColor("#F1F5F9")),
        ('GRID', (0, 0), (-1, -1), 0.5, colors.HexColor("#CBD5E1")),
        ('VALIGN', (0, 0), (-1, -1), 'MIDDLE'),
        ('TOPPADDING', (0, 0), (-1, -1), 4),
        ('BOTTOMPADDING', (0, 0), (-1, -1), 4),
        ('LEFTPADDING', (0, 0), (-1, -1), 5),
        ('RIGHTPADDING', (0, 0), (-1, -1), 5),
        ('ROWBACKGROUNDS', (0, 1), (-1, -1), [colors.white, colors.HexColor("#F8FAFC")]),
    ]))
    story.append(t_status)
    story.append(Spacer(1, 8))

    # Bagian 2: Analisis Screenshot
    story.append(Paragraph("2. Analisis Berdasarkan 3 Tangkapan Layar (Screenshots)", style_h2))

    screen_analysis_html = (
        "<b>• Gambar 1 (digital-aljihad1):</b> Menampilkan status <i>'Suspended by you'</i>. Service ini memiliki angka '1' di belakang karena saat dibuat, nama 'digital-aljihad' sudah dipakai oleh service utama.<br/>"
        "<b>• Gambar 2 (digitalaljihad):</b> Menampilkan status <i>'Suspended by you'</i>. Ini adalah service lama (digitalaljihad001) yang sudah sukses disuspend 11 hari lalu.<br/>"
        "<b>• Gambar 3 (KUNCI PENYEBABNYA - digital.aljihad):</b> Pada halaman <i>Settings</i> service <b>digital.aljihad</b>, di sebelah commit <b>6b68e4b</b> terlihat jelas kotak badge berwarna HIJAU bertuliskan <b>'✓ Live'</b>. Artinya service ini memang <b>BELUM TERSUSPEND</b>."
    )
    t_screen = Table([[Paragraph(screen_analysis_html, style_card)]], colWidths=[180 * mm])
    t_screen.setStyle(TableStyle([
        ('BACKGROUND', (0, 0), (-1, -1), colors.HexColor("#FFFBEB")),
        ('BOX', (0, 0), (-1, -1), 1, colors.HexColor("#FDE68A")),
        ('TOPPADDING', (0, 0), (-1, -1), 6),
        ('BOTTOMPADDING', (0, 0), (-1, -1), 6),
        ('LEFTPADDING', (0, 0), (-1, -1), 8),
        ('RIGHTPADDING', (0, 0), (-1, -1), 8),
    ]))
    story.append(t_screen)

    # Page Break for Step by step guide
    story.append(PageBreak())

    # Bagian 3: Panduan Eksekusi Suspend / Delete
    story.append(Paragraph("3. Panduan Langkah Demi Langkah Mematikan / Menghapus Permanen", style_h2))
    story.append(Paragraph(
        "Karena Anda saat ini sudah membuka halaman <b>Settings</b> untuk service <b>digital.aljihad</b> tersebut, ikuti langkah berikut:",
        style_body
    ))

    guide_steps = [
        "<b>Langkah 1:</b> Pada halaman <i>Settings</i> yang sedang Anda buka, <b>gulir (scroll) terus layar ke bagian paling bawah</b>.",
        "<b>Langkah 2:</b> Anda akan menemukan kotak bergaris merah bernama <b>Danger Zone</b>.",
        "<b>Langkah 3:</b> Di dalamnya terdapat 2 pilihan tombol:<br/>"
        "   • <b>Suspend Web Service</b> (untuk mematikan sementara).<br/>"
        "   • <b>Delete Web Service (SANGAT DIREKOMENDASIKAN)</b>: Menghapus service secara permanen agar dashboard Anda bersih dan tidak membingungkan.",
        "<b>Langkah 4 (Tahap Konfirmasi):</b> Saat tombol diklik, Render akan memunculkan jendela pop-up konfirmasi. Ketik nama service yang diminta (yaitu: <font color='#0F766E'><b>digital.aljihad</b></font>), lalu klik tombol merah konfirmasi <b>Delete</b> / <b>Confirm</b>.",
        "<b>Langkah 5 (Hasil):</b> Badge hijau <i>'✓ Live'</i> akan langsung hilang, dan URL <b>https://digital-aljihad.onrender.com/</b> akan mati seketika."
    ]

    for step in guide_steps:
        t_step = Table([[Paragraph(step, style_card)]], colWidths=[180 * mm])
        t_step.setStyle(TableStyle([
            ('BACKGROUND', (0, 0), (-1, -1), colors.HexColor("#F8FAFC")),
            ('BOX', (0, 0), (-1, -1), 0.5, colors.HexColor("#E2E8F0")),
            ('TOPPADDING', (0, 0), (-1, -1), 4),
            ('BOTTOMPADDING', (0, 0), (-1, -1), 4),
            ('LEFTPADDING', (0, 0), (-1, -1), 8),
            ('RIGHTPADDING', (0, 0), (-1, -1), 8),
        ]))
        story.append(t_step)
        story.append(Spacer(1, 3))

    story.append(Spacer(1, 6))

    # Bagian 4: Keamanan Sistem Aktif Saat Ini
    story.append(Paragraph("4. Mengapa Service Render Ini 100% Aman untuk Dihapus?", style_h2))
    story.append(Paragraph(
        "Aplikasi display digital masjid Anda saat ini sudah sepenuhnya berjalan di infrastruktur baru yang jauh lebih stabil dan cepat. Menghapus service di Render <b>tidak akan mempengaruhi apapun</b> pada layar TV masjid:",
        style_body
    ))

    infra_data = [
        [
            Paragraph("Parameter", style_table_header),
            Paragraph("Layanan Render (Lama / Dihapus)", style_table_header),
            Paragraph("Layanan Resmi Aktif Sekarang", style_table_header)
        ],
        [
            Paragraph("<b>Alamat URL Utama</b>", style_table_cell),
            Paragraph("digital-aljihad.onrender.com", style_table_cell),
            Paragraph("<b><font color='#0F766E'>https://digitalaljihad.my.id/</font></b>", style_table_cell)
        ],
        [
            Paragraph("<b>Platform Hosting</b>", style_table_cell),
            Paragraph("Render Free (Sering cold start / sleep)", style_table_cell),
            Paragraph("<b>Cloudflare Pages Global CDN</b> (Instan, 100% Uptime)", style_table_cell)
        ],
        [
            Paragraph("<b>Database Cloud</b>", style_table_cell),
            Paragraph("TiDB Cloud Serverless", style_table_cell),
            Paragraph("<b>Supabase PostgreSQL Cloud Realtime</b>", style_table_cell)
        ],
        [
            Paragraph("<b>Repositori GitHub</b>", style_table_cell),
            Paragraph("mydowndrive-ops/digitalaljihad001", style_table_cell),
            Paragraph("<b>archivedaljihad-cloud/digitalaljihad</b>", style_table_cell)
        ]
    ]

    t_infra = Table(infra_data, colWidths=[40 * mm, 65 * mm, 75 * mm])
    t_infra.setStyle(TableStyle([
        ('BACKGROUND', (0, 0), (-1, 0), colors.HexColor("#F1F5F9")),
        ('GRID', (0, 0), (-1, -1), 0.5, colors.HexColor("#CBD5E1")),
        ('VALIGN', (0, 0), (-1, -1), 'MIDDLE'),
        ('TOPPADDING', (0, 0), (-1, -1), 4),
        ('BOTTOMPADDING', (0, 0), (-1, -1), 4),
        ('LEFTPADDING', (0, 0), (-1, -1), 5),
        ('RIGHTPADDING', (0, 0), (-1, -1), 5),
        ('ROWBACKGROUNDS', (0, 1), (-1, -1), [colors.white, colors.HexColor("#F0FDF4")]),
    ]))
    story.append(t_infra)
    story.append(Spacer(1, 8))

    # Bagian 5: Kesiapan Mode Sholat Jumat
    story.append(Paragraph("5. Kesiapan Mode Sholat Jum'at Besok (2 Oktober 2026)", style_h2))
    jumat_info_html = (
        "<b>🕌 STATUS PRAYER MODE JUM'AT: SUDAH 100% OTOMATIS</b><br/>"
        "Logika sistem di <b>https://digitalaljihad.my.id/</b> secara otomatis mendeteksi hari Jum'at (<code>getDay() === 5</code>) saat waktu Dzuhur tiba (<b>11:45 WIB</b>):<br/>"
        "• <b>11:40 - 11:45 WIB:</b> Hitung mundur adzan & audio tarhim.<br/>"
        "• <b>11:45 - 11:50 WIB:</b> Waktu adzan berkumandang.<br/>"
        "• <b>11:50 - 12:40 WIB (50 Menit):</b> Layar terkunci tenang dalam <b>Khutbah & Sholat Jum'at</b>, menampilkan 4 plakat petugas resmi (Khatib, Imam, Muadzin, Bilal) serta hadits larangan berkata sia-sia saat khatib berkhutbah.<br/>"
        "• <b>12:40 WIB:</b> Layar otomatis kembali ke rotasi informasi normal."
    )
    t_jumat = Table([[Paragraph(jumat_info_html, style_card)]], colWidths=[180 * mm])
    t_jumat.setStyle(TableStyle([
        ('BACKGROUND', (0, 0), (-1, -1), colors.HexColor("#F0FDF4")),
        ('BOX', (0, 0), (-1, -1), 1, colors.HexColor("#86EFAC")),
        ('TOPPADDING', (0, 0), (-1, -1), 6),
        ('BOTTOMPADDING', (0, 0), (-1, -1), 6),
        ('LEFTPADDING', (0, 0), (-1, -1), 8),
        ('RIGHTPADDING', (0, 0), (-1, -1), 8),
    ]))
    story.append(t_jumat)

    # Build Document
    doc.build(story, canvasmaker=NumberedCanvas)
    print(f"SUCCESS: PDF generated at {filename}")

if __name__ == "__main__":
    out_path = sys.argv[1] if len(sys.argv) > 1 else "PANDUAN_AKUN_RENDER.pdf"
    build_pdf(out_path)
