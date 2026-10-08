# -*- coding: utf-8 -*-
"""
Script Generator PDF: Riwayat Percakapan Teknis Status Deployment & Repositori GitHub
Masjid Jami' Al-Jihad Graha Asri
"""

import os
import sys
import re
import html
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
            self.drawString(14 * mm, 285 * mm, "RIWAYAT KONSULTASI TEKNIS • DEPLOYMENT & GITHUB")
            self.drawRightString(196 * mm, 285 * mm, "MASJID JAMI' AL-JIHAD GRAHA ASRI")
            self.setStrokeColor(colors.HexColor("#CBD5E1"))
            self.setLineWidth(0.5)
            self.line(14 * mm, 282 * mm, 196 * mm, 282 * mm)
            
        # Footer
        self.setStrokeColor(colors.HexColor("#CBD5E1"))
        self.setLineWidth(0.5)
        self.line(14 * mm, 14 * mm, 196 * mm, 14 * mm)
        
        self.setFont("Helvetica", 8)
        self.setFillColor(colors.HexColor("#64748B"))
        footer_text = "Dokumentasi Riwayat Chat Sesi Aktif • ID Sesi: 51ed5061-aca6-4535-89cf-d9e293922daf"
        page_str = f"Halaman {self._pageNumber} dari {page_count}"
        self.drawString(14 * mm, 10 * mm, footer_text)
        self.drawRightString(196 * mm, 10 * mm, page_str)
        self.restoreState()


def clean_markdown_to_reportlab(text):
    """Konversi markdown sederhana menjadi XML tag ReportLab yang valid dan aman."""
    # Bersihkan emoji yang tidak ada di Helvetica
    replacements = {
        '🕌': '', '👑': '[Admin]', '💼': '[Bendahara]', '📺': '[Operator]',
        '🟢': '[AKTIF]', '🔴': '[NONAKTIF/SUSPEND]', '⚪': '[TIDAK ADA]',
        '📊': '', '🔍': '', '💡': '[INFO]', '📌': '[CATATAN]', '👉': '->',
        '🏛': '', '🌐': '', '✓': '[OK]', '•': '&bull;', 'â€"': '-', 'â€“': '-'
    }
    for k, v in replacements.items():
        text = text.replace(k, v)
        
    # Escape ampersand
    text = re.sub(r'&(?!amp;|lt;|gt;|quot;|apos;|bull;)', '&amp;', text)
    
    # Bold **text** -> <b>text</b>
    text = re.sub(r'\*\*(.*?)\*\*', r'<b>\1</b>', text)
    
    # Italic *text* -> <i>text</i>
    text = re.sub(r'\*(.*?)\*', r'<i>\1</i>', text)
    
    # Inline code `text` -> <font name="Courier" color="#0F766E"><b>text</b></font>
    text = re.sub(r'`(.*?)`', r'<font name="Courier" color="#0F766E"><b>\1</b></font>', text)
    
    return text


def build_chat_pdf(output_pdf):
    doc = SimpleDocTemplate(
        output_pdf,
        pagesize=A4,
        leftMargin=14 * mm,
        rightMargin=14 * mm,
        topMargin=15 * mm,
        bottomMargin=18 * mm
    )

    styles = getSampleStyleSheet()

    # Style definitions
    style_h1 = ParagraphStyle(
        'H1', parent=styles['Normal'],
        fontName='Helvetica-Bold', fontSize=15, leading=19,
        textColor=colors.HexColor('#064E3B')
    )
    style_subtitle = ParagraphStyle(
        'Sub', parent=styles['Normal'],
        fontName='Helvetica', fontSize=9, leading=13,
        textColor=colors.HexColor('#047857')
    )
    style_badge = ParagraphStyle(
        'Badge', parent=styles['Normal'],
        fontName='Helvetica-Bold', fontSize=7.5, leading=10,
        textColor=colors.HexColor('#B45309'), alignment=2
    )

    style_user_header = ParagraphStyle(
        'UserH', parent=styles['Normal'],
        fontName='Helvetica-Bold', fontSize=9.5, leading=12,
        textColor=colors.HexColor('#1E3A8A')
    )
    style_user_body = ParagraphStyle(
        'UserB', parent=styles['Normal'],
        fontName='Helvetica-Bold', fontSize=10.5, leading=14,
        textColor=colors.HexColor('#0F172A')
    )

    style_bot_header = ParagraphStyle(
        'BotH', parent=styles['Normal'],
        fontName='Helvetica-Bold', fontSize=9.5, leading=12,
        textColor=colors.HexColor('#065F46')
    )
    style_bot_body = ParagraphStyle(
        'BotB', parent=styles['Normal'],
        fontName='Helvetica', fontSize=9, leading=13,
        textColor=colors.HexColor('#1E293B')
    )
    style_bot_bullet = ParagraphStyle(
        'BotBullet', parent=styles['Normal'],
        fontName='Helvetica', fontSize=8.8, leading=12.5,
        leftIndent=12, firstLineIndent=-8,
        textColor=colors.HexColor('#1E293B')
    )
    style_bot_subhead = ParagraphStyle(
        'BotSub', parent=styles['Normal'],
        fontName='Helvetica-Bold', fontSize=10, leading=13,
        textColor=colors.HexColor('#0F766E'), spaceBefore=4, spaceAfter=2
    )

    style_th = ParagraphStyle(
        'TH', parent=styles['Normal'],
        fontName='Helvetica-Bold', fontSize=8, leading=10,
        textColor=colors.white, alignment=1
    )
    style_td = ParagraphStyle(
        'TD', parent=styles['Normal'],
        fontName='Helvetica', fontSize=7.8, leading=10.5,
        textColor=colors.HexColor('#1E293B')
    )
    style_td_center = ParagraphStyle(
        'TDC', parent=styles['Normal'],
        fontName='Helvetica-Bold', fontSize=7.8, leading=10.5,
        textColor=colors.HexColor('#1E293B'), alignment=1
    )

    story = []

    # 1. HEADER DOKUMEN
    header_data = [
        [
            Paragraph("<b>DOKUMENTASI RESMI RIWAYAT KONSULTASI TEKNIS</b><br/>"
                      "<font size=8.5 color='#047857'>Sistem Informasi Digital Masjid Jami' Al-Jihad • Status Deployment Cloud &amp; GitHub</font>", style_h1),
            Paragraph("<b>KLASIFIKASI: ARSIP RESMI</b><br/>"
                      "Tanggal: 8 Oktober 2026<br/>"
                      "Waktu: 23:00 WIB", style_badge)
        ]
    ]
    t_header = Table(header_data, colWidths=[125 * mm, 57 * mm])
    t_header.setStyle(TableStyle([
        ('VALIGN', (0, 0), (-1, -1), 'TOP'),
        ('BOTTOMPADDING', (0, 0), (-1, -1), 4),
        ('TOPPADDING', (0, 0), (-1, -1), 0),
        ('LEFTPADDING', (0, 0), (-1, -1), 0),
        ('RIGHTPADDING', (0, 0), (-1, -1), 0),
    ]))
    story.append(t_header)
    story.append(Spacer(1, 2 * mm))
    story.append(HRFlowable(width="100%", thickness=2, color=colors.HexColor("#065F46"), spaceAfter=4 * mm))

    # Info Kotak Singkat
    meta_box = [
        [
            Paragraph("<b>Ringkasan Topik Konsultasi:</b> Dokumen ini merekam secara utuh percakapan tanya-jawab teknis mengenai pemetaan lokasi deployment versi Web Dinamis lama (Render.com, Vercel, Lokal), pengujian status live server Render real-time, kepemilikan akun GitHub lama (<font name='Courier'>mydowndrive-ops</font>), serta integrasi repositori Web Statis Cloudflare aktif saat ini (<font name='Courier'>archivedaljihad-cloud/digitalaljihad</font>).", style_subtitle)
        ]
    ]
    t_meta = Table(meta_box, colWidths=[182 * mm])
    t_meta.setStyle(TableStyle([
        ('BACKGROUND', (0, 0), (-1, -1), colors.HexColor("#F0FDF4")),
        ('BOX', (0, 0), (-1, -1), 1, colors.HexColor("#86EFAC")),
        ('LEFTPADDING', (0, 0), (-1, -1), 10),
        ('RIGHTPADDING', (0, 0), (-1, -1), 10),
        ('TOPPADDING', (0, 0), (-1, -1), 6),
        ('BOTTOMPADDING', (0, 0), (-1, -1), 6),
    ]))
    story.append(t_meta)
    story.append(Spacer(1, 5 * mm))

    # Helper untuk Card Percakapan
    def create_user_bubble(q_num, text):
        content = [
            Paragraph(f"<b>[SESI TANYA #{q_num}] PENGGUNA (USER)</b>", style_user_header),
            Spacer(1, 1 * mm),
            Paragraph(f"\"{clean_markdown_to_reportlab(text)}\"", style_user_body)
        ]
        t = Table([[content]], colWidths=[182 * mm])
        t.setStyle(TableStyle([
            ('BACKGROUND', (0, 0), (-1, -1), colors.HexColor("#EFF6FF")),
            ('BOX', (0, 0), (-1, -1), 1.2, colors.HexColor("#93C5FD")),
            ('LEFTPADDING', (0, 0), (-1, -1), 10),
            ('RIGHTPADDING', (0, 0), (-1, -1), 10),
            ('TOPPADDING', (0, 0), (-1, -1), 7),
            ('BOTTOMPADDING', (0, 0), (-1, -1), 7),
        ]))
        return t

    def create_bot_card(q_num, flowables_content):
        header_p = Paragraph(f"<b>[JAWABAN #{q_num}] ASISTEN ANTIGRAVITY AI</b>", style_bot_header)
        all_flowables = [header_p, Spacer(1, 1.5 * mm)] + flowables_content
        t = Table([[all_flowables]], colWidths=[182 * mm])
        t.setStyle(TableStyle([
            ('BACKGROUND', (0, 0), (-1, -1), colors.HexColor("#FFFFFF")),
            ('BOX', (0, 0), (-1, -1), 1, colors.HexColor("#CBD5E1")),
            ('LEFTPADDING', (0, 0), (-1, -1), 10),
            ('RIGHTPADDING', (0, 0), (-1, -1), 10),
            ('TOPPADDING', (0, 0), (-1, -1), 8),
            ('BOTTOMPADDING', (0, 0), (-1, -1), 8),
        ]))
        return t

    # ==========================================
    # PERTANYAAN 1
    # ==========================================
    story.append(create_user_bubble(1, "Apakah anda masih ingat, web yang versi dinamis saya deploy dimana saja"))
    story.append(Spacer(1, 2.5 * mm))

    ans1_content = [
        Paragraph("Tentu, saya masih ingat dengan sangat jelas seluruh riwayat dan tempat deployment <b>versi web dinamis (Laravel + PHP + Database)</b> Anda sebelumnya:", style_bot_body),
        Spacer(1, 2 * mm),
        Paragraph("<b>1. Render.com (Platform Cloud Utama Versi Dinamis)</b>", style_bot_subhead),
        Paragraph("Ini adalah tempat hosting paling lama dan paling stabil saat menjalankan versi dinamis Laravel via Docker (Nginx + PHP-FPM):", style_bot_body),
        Paragraph("&bull; <b>Akun Render:</b> Terhubung ke GitHub <b>mydowndrive-ops</b> (<i>my.downdrive@gmail.com</i>) dari repositori asal <b>digitalaljihad001</b>.", style_bot_bullet),
        Paragraph("&bull; <b>Alamat URL Layanan:</b>", style_bot_bullet),
        Paragraph("&nbsp;&nbsp;&nbsp;&nbsp;1. <b>https://digital-aljihad.onrender.com/</b> (Service utama: <font name='Courier'>digital.aljihad</font>, Service ID: <font name='Courier'>srv-daffllou01pc73a98acg</font>. Sempat dipasang UptimeRobot ping 5 menit).", style_bot_bullet),
        Paragraph("&nbsp;&nbsp;&nbsp;&nbsp;2. <b>https://digitalaljihad001.onrender.com/</b> (Service generasi awal saat pertama kali setup, kemudian di-suspend).", style_bot_bullet),
        Paragraph("&nbsp;&nbsp;&nbsp;&nbsp;3. <b>https://digital-aljihad1.onrender.com/</b> (Service percobaan/cadangan).", style_bot_bullet),
        Paragraph("&bull; <b>Basis Data Cloud:</b> Terhubung ke <b>TiDB Cloud Serverless</b> (<font name='Courier'>gateway01.ap-southeast-1.prod.aws.tidbcloud.com:4000</font>, database <font name='Courier'>digitalv304</font>).", style_bot_bullet),
        Spacer(1, 2 * mm),
        Paragraph("<b>2. Vercel (Uji Coba Serverless)</b>", style_bot_subhead),
        Paragraph("Sebelum pindah ke Render, Anda sempat mencoba mendeploy Laravel ke Vercel:", style_bot_body),
        Paragraph("&bull; <b>Alamat URL:</b> Ditargetkan ke domain gratis bawaan Vercel (contoh: <b>https://digitalaljihad001.vercel.app</b>).", style_bot_bullet),
        Paragraph("&bull; <b>Konfigurasi Berkas:</b> <font name='Courier'>vercel.json</font> &amp; <font name='Courier'>Dockerfile.vercel</font>.", style_bot_bullet),
        Paragraph("&bull; <b>Koneksi Database:</b> Sempat dicoba disambungkan ke <b>Supabase PostgreSQL Pooler</b> dan TiDB Cloud.", style_bot_bullet),
        Paragraph("&bull; <b>Catatan:</b> Mengalami kendala runtime PHP serverless (build timeout, pop-up redeploy berbayar, dan prompt download file), sehingga beralih ke Render Docker.", style_bot_bullet),
        Spacer(1, 2 * mm),
        Paragraph("<b>3. Server Lokal / On-Premise (Komputer &amp; Laptop Masjid)</b>", style_bot_subhead),
        Paragraph("&bull; <b>Runtime:</b> XAMPP / Laragon / <font name='Courier'>php artisan serve</font>.", style_bot_bullet),
        Paragraph("&bull; <b>Akses Lokal:</b> <font name='Courier'>http://127.0.0.1:8000</font> atau <font name='Courier'>http://localhost/sholat_jumat</font>.", style_bot_bullet),
        Paragraph("&bull; <b>Database:</b> MySQL lokal (<font name='Courier'>digitalv304.sql</font>).", style_bot_bullet),
        Spacer(1, 2 * mm),
        Paragraph("<b>[INFO] Kilas Balik Transformasi:</b> Karena pada versi dinamis di atas sering menghadapi masalah server tidur (cold start), kuota gratis terbatas, dan risiko database corrupt saat mati listrik tanpa UPS, sistem akhirnya direvolusi total 1000% menjadi <b>Web Statis Modern</b> di <b>Cloudflare Workers (https://digitalaljihad.my.id/)</b> dan <b>Supabase Realtime</b>.", style_subtitle)
    ]
    story.append(create_bot_card(1, ans1_content))
    story.append(Spacer(1, 4.5 * mm))

    # ==========================================
    # PERTANYAAN 2
    # ==========================================
    story.append(create_user_bubble(2, "bagaimana status web di render.com"))
    story.append(Spacer(1, 2.5 * mm))

    # Buat tabel status render
    table_render_data = [
        [
            Paragraph("<b>No</b>", style_th),
            Paragraph("<b>Alamat URL Service</b>", style_th),
            Paragraph("<b>Status HTTP</b>", style_th),
            Paragraph("<b>Kondisi Riil</b>", style_th),
            Paragraph("<b>Keterangan Header Jaringan</b>", style_th)
        ],
        [
            Paragraph("1", style_td_center),
            Paragraph("<b>digital-aljihad.onrender.com</b>", style_td),
            Paragraph("<font color='#991B1B'><b>503 Unavailable</b></font>", style_td_center),
            Paragraph("<font color='#991B1B'><b>[NONAKTIF]</b></font>", style_td_center),
            Paragraph("<font name='Courier'>x-render-routing: suspend-by-user</font><br/>(Berhasil di-suspend oleh Anda)", style_td)
        ],
        [
            Paragraph("2", style_td_center),
            Paragraph("<b>digitalaljihad001.onrender.com</b>", style_td),
            Paragraph("<font color='#166534'><b>200 OK</b></font>", style_td_center),
            Paragraph("<font color='#166534'><b>[AKTIF / LIVE]</b></font>", style_td_center),
            Paragraph("<font name='Courier'>x-powered-by: PHP/8.2.7</font><br/>(Melayani Laravel rotator.blade.php)", style_td)
        ],
        [
            Paragraph("3", style_td_center),
            Paragraph("<b>digitalaljihad1.onrender.com</b>", style_td),
            Paragraph("<font color='#166534'><b>200 OK</b></font>", style_td_center),
            Paragraph("<font color='#166534'><b>[AKTIF / LIVE]</b></font>", style_td_center),
            Paragraph("<font name='Courier'>x-powered-by: PHP/8.2.7</font><br/>(Melayani Laravel rotator.blade.php)", style_td)
        ],
        [
            Paragraph("4", style_td_center),
            Paragraph("<b>digitalaljihad.onrender.com</b>", style_td),
            Paragraph("<font color='#64748B'><b>404 Not Found</b></font>", style_td_center),
            Paragraph("<font color='#64748B'><b>[TIDAK ADA]</b></font>", style_td_center),
            Paragraph("<font name='Courier'>x-render-routing: no-server</font><br/>(Subdomain tidak pernah dibuat)", style_td)
        ]
    ]
    t_render = Table(table_render_data, colWidths=[8 * mm, 46 * mm, 26 * mm, 26 * mm, 68 * mm])
    t_render.setStyle(TableStyle([
        ('BACKGROUND', (0, 0), (-1, 0), colors.HexColor("#065F46")),
        ('BOX', (0, 0), (-1, -1), 0.8, colors.HexColor("#CBD5E1")),
        ('INNERGRID', (0, 0), (-1, -1), 0.5, colors.HexColor("#E2E8F0")),
        ('VALIGN', (0, 0), (-1, -1), 'MIDDLE'),
        ('TOPPADDING', (0, 0), (-1, -1), 4),
        ('BOTTOMPADDING', (0, 0), (-1, -1), 4),
        ('LEFTPADDING', (0, 0), (-1, -1), 4),
        ('RIGHTPADDING', (0, 0), (-1, -1), 4),
        ('BACKGROUND', (0, 1), (-1, 1), colors.HexColor("#FEF2F2")),
        ('BACKGROUND', (0, 2), (-1, 2), colors.HexColor("#F0FDF4")),
        ('BACKGROUND', (0, 3), (-1, 3), colors.HexColor("#F0FDF4")),
        ('BACKGROUND', (0, 4), (-1, 4), colors.HexColor("#F8FAFC")),
    ]))

    ans2_content = [
        Paragraph("Pengecekan diagnostik jaringan langsung (live network inspection) ke edge server Render.com menunjukkan hasil real-time berikut:", style_bot_body),
        Spacer(1, 2 * mm),
        t_render,
        Spacer(1, 2.5 * mm),
        Paragraph("<b>Analisis &amp; Rekomendasi:</b>", style_bot_subhead),
        Paragraph("&bull; Service <b>digital-aljihad.onrender.com</b> telah sukses dinonaktifkan (<i>suspended</i>) sehingga aman tidak mengonsumsi jam kuota gratis bulanan.", style_bot_bullet),
        Paragraph("&bull; Namun dua service lama yaitu <b>digitalaljihad001</b> dan <b>digitalaljihad1</b> ternyata masih berstatus <b>200 OK (LIVE)</b> di akun Render Anda dan masih merender template Laravel.", style_bot_bullet),
        Paragraph("&bull; Jika ingin menghemat kuota 750 jam/bulan akun Render, Anda disarankan membuka <font name='Courier'>dashboard.render.com</font> &gt; Settings &gt; klik <b>Suspend Service</b> atau <b>Delete Service</b> pada kedua service tersebut.", style_bot_bullet)
    ]
    story.append(create_bot_card(2, ans2_content))
    story.append(Spacer(1, 4.5 * mm))

    # ==========================================
    # PERTANYAAN 3
    # ==========================================
    story.append(create_user_bubble(3, "https://digitalaljihad001.onrender.com & https://digitalaljihad1.onrender.com apakah ini 1 akun github"))
    story.append(Spacer(1, 2.5 * mm))

    ans3_content = [
        Paragraph("<b>Ya, betul! Keduanya berasal dari 1 akun GitHub yang sama.</b>", style_bot_subhead),
        Spacer(1, 1.5 * mm),
        Paragraph("<b>1. Akun GitHub yang Terhubung:</b>", style_bot_body),
        Paragraph("&bull; <b>Username GitHub:</b> <font name='Courier'><b>mydowndrive-ops</b></font>", style_bot_bullet),
        Paragraph("&bull; <b>Alamat Email Akun:</b> <font name='Courier'><b>my.downdrive@gmail.com</b></font>", style_bot_bullet),
        Paragraph("&bull; <b>Nama Repositori GitHub:</b> <font name='Courier'><b>digitalaljihad001</b></font>", style_bot_bullet),
        Spacer(1, 2 * mm),
        Paragraph("<b>2. Mengapa di Render Ada 2 Nama Berbeda?</b>", style_bot_body),
        Paragraph("Kedua service tersebut berada di dalam <b>1 akun Render</b> yang sama:", style_bot_body),
        Paragraph("&bull; <b>digitalaljihad001.onrender.com:</b> Web Service pertama yang dibuat di Render saat menghubungkan repositori <font name='Courier'>digitalaljihad001</font>. Render secara default menyamakan nama subdomain dengan nama repo.", style_bot_bullet),
        Paragraph("&bull; <b>digitalaljihad1.onrender.com:</b> Web Service kedua yang dibuat kemudian di akun Render yang sama, bersumber dari repo yang sama untuk skenario cadangan (switcher dua server).", style_bot_bullet),
        Spacer(1, 2 * mm),
        Paragraph("<b>Perbandingan dengan Akun Sekarang:</b>", style_bot_body),
        Paragraph("&bull; <b>Akun GitHub Lama (Era Render / Laravel):</b> <font name='Courier'>mydowndrive-ops</font> (Repo: <font name='Courier'>digitalaljihad001</font>)", style_bot_bullet),
        Paragraph("&bull; <b>Akun GitHub Baru (Era Cloudflare / Web Statis):</b> <font name='Courier'>archivedaljihad-cloud</font> (Repo: <font name='Courier'>digitalaljihad</font>)", style_bot_bullet)
    ]
    story.append(create_bot_card(3, ans3_content))
    story.append(Spacer(1, 4.5 * mm))

    # ==========================================
    # PERTANYAAN 4
    # ==========================================
    story.append(create_user_bubble(4, "kalau yang web statis ini terhubung ke github mana"))
    story.append(Spacer(1, 2.5 * mm))

    ans4_content = [
        Paragraph("Web statis yang aktif saat ini di TV Display masjid terhubung ke repositori GitHub resmi berikut:", style_bot_body),
        Spacer(1, 2 * mm),
        Paragraph("<b>Identitas Repositori GitHub Resmi (Web Statis):</b>", style_bot_subhead),
        Paragraph("&bull; <b>Akun / Organisasi GitHub:</b> <font name='Courier'><b>archivedaljihad-cloud</b></font>", style_bot_bullet),
        Paragraph("&bull; <b>Nama Repositori:</b> <font name='Courier'><b>digitalaljihad</b></font>", style_bot_bullet),
        Paragraph("&bull; <b>Tautan Web Repositori:</b> <b>https://github.com/archivedaljihad-cloud/digitalaljihad</b>", style_bot_bullet),
        Paragraph("&bull; <b>URL Git Remote (Fetch &amp; Push):</b> <font name='Courier'>https://github.com/archivedaljihad-cloud/digitalaljihad.git</font>", style_bot_bullet),
        Paragraph("&bull; <b>Branch Produksi:</b> <font name='Courier'><b>main</b></font>", style_bot_bullet),
        Paragraph("&bull; <b>Email Resmi Terkait:</b> <font name='Courier'>archived.aljihad@gmail.com</font>", style_bot_bullet),
        Spacer(1, 2 * mm),
        Paragraph("<b>Jalur Sinkronisasi Otomatis:</b>", style_bot_subhead),
        Paragraph("1. Seluruh aset kode statis (folder <font name='Courier'>web-statis/</font>) terhubung langsung ke <b>Cloudflare Workers</b> (<font name='Courier'>digitalaljihad.my.id</font>).", style_bot_bullet),
        Paragraph("2. Basis data real-time (<100ms) terhubung ke <b>Supabase Cloud PostgreSQL</b>.", style_bot_bullet),
        Paragraph("3. Repositori GitHub <font name='Courier'>archivedaljihad-cloud/digitalaljihad</font> bertindak sebagai pusat source code utama.", style_bot_bullet)
    ]
    story.append(create_bot_card(4, ans4_content))
    story.append(Spacer(1, 4.5 * mm))

    # ==========================================
    # PERTANYAAN 5 (PERMINTAAN PDF)
    # ==========================================
    story.append(create_user_bubble(5, "Buatkan file pdf semua riwayat chat di halaman ini.Tidak perlu menjalankan 4 pilar utama anda"))
    story.append(Spacer(1, 2.5 * mm))

    ans5_content = [
        Paragraph("<b>Permintaan Diproses:</b> Dokumen PDF resmi ini langsung disusun dan diterbitkan sesuai permintaan Anda tanpa memicu 4 pilar otomatisasi (tanpa commit/push, tanpa deploy cloudflare, tanpa sync database, dan tanpa sync folder lokal).", style_bot_body),
        Spacer(1, 1.5 * mm),
        Paragraph("&bull; <b>Status Eksekusi:</b> 100% Khusus pembuatan berkas PDF dokumentasi sesi.", style_bot_bullet),
        Paragraph("&bull; <b>Nama Berkas:</b> <font name='Courier'><b>RIWAYAT_CHAT_SESI_DEPLOYMENT.pdf</b></font> &amp; versi HTML pendukung.", style_bot_bullet)
    ]
    story.append(create_bot_card(5, ans5_content))

    # Build Document
    doc.build(story, canvasmaker=NumberedCanvas)
    print(f"[OK] Berhasil membuat PDF: {output_pdf}")


if __name__ == '__main__':
    target_pdf = os.path.join(r"C:\Users\anthu\Documents\【Project】\DIGITALv304", "RIWAYAT_CHAT_SESI_DEPLOYMENT.pdf")
    build_chat_pdf(target_pdf)
