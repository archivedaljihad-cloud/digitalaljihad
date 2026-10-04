# -*- coding: utf-8 -*-
import os
import sys
if sys.platform.startswith('win'):
    try:
        sys.stdout.reconfigure(encoding='utf-8')
    except Exception:
        pass
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
            self.drawString(15 * mm, 285 * mm, "PANDUAN PENGATURAN FULLY KIOSK BROWSER DI SMART TV")
            self.drawRightString(195 * mm, 285 * mm, "MASJID JAMI' AL JIHAD GRAHA ASRI")
            self.setStrokeColor(colors.HexColor("#CBD5E1"))
            self.setLineWidth(0.5)
            self.line(15 * mm, 282 * mm, 195 * mm, 282 * mm)
            
        # Footer
        self.setStrokeColor(colors.HexColor("#CBD5E1"))
        self.setLineWidth(0.5)
        self.line(15 * mm, 15 * mm, 195 * mm, 15 * mm)
        
        self.setFont("Helvetica", 8)
        self.setFillColor(colors.HexColor("#64748B"))
        footer_text = "Dokumen Panduan Teknis TV Signage • Masjid Jami' Al Jihad • Data Center: Cloudflare & GitHub"
        page_str = f"Halaman {self._pageNumber} dari {page_count}"
        self.drawString(15 * mm, 11 * mm, footer_text)
        self.drawRightString(195 * mm, 11 * mm, page_str)
        self.restoreState()

def create_pdf(output_filename):
    doc = SimpleDocTemplate(
        output_filename,
        pagesize=A4,
        leftMargin=15 * mm,
        rightMargin=15 * mm,
        topMargin=15 * mm,
        bottomMargin=18 * mm
    )

    styles = getSampleStyleSheet()

    # Custom styles
    title_style = ParagraphStyle(
        'DocTitle',
        fontName='Helvetica-Bold',
        fontSize=15,
        leading=18,
        textColor=colors.HexColor("#064E3B")
    )
    subtitle_style = ParagraphStyle(
        'DocSubtitle',
        fontName='Helvetica',
        fontSize=9,
        leading=12,
        textColor=colors.HexColor("#047857")
    )
    badge_style = ParagraphStyle(
        'BadgeText',
        fontName='Helvetica-Bold',
        fontSize=8,
        leading=10,
        alignment=2,
        textColor=colors.HexColor("#D97706")
    )
    h1_style = ParagraphStyle(
        'Heading1_Custom',
        fontName='Helvetica-Bold',
        fontSize=11,
        leading=14,
        textColor=colors.HexColor("#065F46"),
        spaceBefore=8,
        spaceAfter=4
    )
    body_style = ParagraphStyle(
        'Body_Custom',
        fontName='Helvetica',
        fontSize=8.5,
        leading=11.5,
        textColor=colors.HexColor("#1E293B")
    )
    body_bold = ParagraphStyle(
        'Body_Bold',
        fontName='Helvetica-Bold',
        fontSize=8.5,
        leading=11.5,
        textColor=colors.HexColor("#0F172A")
    )
    callout_style = ParagraphStyle(
        'Callout',
        fontName='Helvetica',
        fontSize=8,
        leading=11,
        textColor=colors.HexColor("#0F172A")
    )
    tbl_header_style = ParagraphStyle(
        'TblHeader',
        fontName='Helvetica-Bold',
        fontSize=8,
        leading=10,
        textColor=colors.white
    )
    tbl_cell_style = ParagraphStyle(
        'TblCell',
        fontName='Helvetica',
        fontSize=8,
        leading=10.5,
        textColor=colors.HexColor("#1E293B")
    )
    tbl_cell_bold = ParagraphStyle(
        'TblCellBold',
        fontName='Helvetica-Bold',
        fontSize=8,
        leading=10.5,
        textColor=colors.HexColor("#0F172A")
    )
    tbl_badge_green = ParagraphStyle(
        'TblBadgeGreen',
        fontName='Helvetica-Bold',
        fontSize=7.5,
        leading=9.5,
        textColor=colors.HexColor("#047857")
    )
    tbl_badge_amber = ParagraphStyle(
        'TblBadgeAmber',
        fontName='Helvetica-Bold',
        fontSize=7.5,
        leading=9.5,
        textColor=colors.HexColor("#B45309")
    )
    tbl_badge_blue = ParagraphStyle(
        'TblBadgeBlue',
        fontName='Helvetica-Bold',
        fontSize=7.5,
        leading=9.5,
        textColor=colors.HexColor("#0369A1")
    )

    story = []

    # ==========================
    # HEADER BANNER
    # ==========================
    header_data = [
        [
            Paragraph("<b>PANDUAN SETINGAN FULLY KIOSK BROWSER</b><br/><font color='#047857' size='8.5'>Smart TV & Android TV Box Display Masjid Jami' Al-Jihad Graha Asri</font>", title_style),
            Paragraph("<b>DATA CENTER UTAMA</b><br/><font color='#065F46'>Cloudflare & GitHub</font><br/><font color='#64748B' size='7'>Domain: digitalaljihad.my.id</font>", badge_style)
        ]
    ]
    t_header = Table(header_data, colWidths=[120 * mm, 60 * mm])
    t_header.setStyle(TableStyle([
        ('VALIGN', (0,0), (-1,-1), 'MIDDLE'),
        ('BACKGROUND', (0,0), (-1,-1), colors.HexColor("#ECFDF5")),
        ('BOX', (0,0), (-1,-1), 1, colors.HexColor("#10B981")),
        ('LEFTPADDING', (0,0), (-1,-1), 8),
        ('RIGHTPADDING', (0,0), (-1,-1), 8),
        ('TOPPADDING', (0,0), (-1,-1), 6),
        ('BOTTOMPADDING', (0,0), (-1,-1), 6),
    ]))
    story.append(t_header)
    story.append(Spacer(1, 4 * mm))

    # ==========================
    # CALLOUT UTAMA
    # ==========================
    callout_data = [
        [
            Paragraph(
                "<b>🌐 URL Produksi Utama (Start URL):</b> <font color='#065F46'><b>https://digitalaljihad.my.id/</b></font><br/>"
                "<b>⚡ Arsitektur Baru:</b> Cloudflare Workers (Edge Jakarta) + GitHub main + Supabase Realtime Database. "
                "Seluruh pembaruan dari HP/laptop pengurus masjid tersinkronisasi seketika (&lt;1 detik) ke layar TV tanpa reload berat.",
                callout_style
            )
        ]
    ]
    t_callout = Table(callout_data, colWidths=[180 * mm])
    t_callout.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,-1), colors.HexColor("#F8FAFC")),
        ('LINELEFT', (0,0), (-1,-1), 3, colors.HexColor("#065F46")),
        ('BOX', (0,0), (-1,-1), 0.5, colors.HexColor("#E2E8F0")),
        ('LEFTPADDING', (0,0), (-1,-1), 8),
        ('RIGHTPADDING', (0,0), (-1,-1), 8),
        ('TOPPADDING', (0,0), (-1,-1), 5),
        ('BOTTOMPADDING', (0,0), (-1,-1), 5),
    ]))
    story.append(t_callout)
    story.append(Spacer(1, 3 * mm))

    # ==========================
    # BAGIAN 1: INITIAL SCALE & VIEWPORT
    # ==========================
    story.append(Paragraph("1. Mengatasi Tampilan Terlalu Besar (Zoom In) / Terpotong di TV 55\" – 75\" / 4K", h1_style))
    story.append(Paragraph(
        "<i>Penyebab Teknis:</i> Sistem Android TV memiliki kepadatan piksel (DPI) tinggi sehingga WebView otomatis melakukan <b>auto zoom-in 150%–200%</b>. Lakukan pengaturan berikut di Fully Kiosk agar tampilan pas 100%:",
        body_style
    ))
    story.append(Spacer(1, 2 * mm))

    tbl1_data = [
        [Paragraph("Menu di Fully Kiosk TV", tbl_header_style), Paragraph("Nilai yang Dipilih", tbl_header_style), Paragraph("Fungsi & Catatan Teknis", tbl_header_style)],
        [
            Paragraph("Web Content &gt;<br/><b>Initial Scale</b>", tbl_cell_bold),
            Paragraph("<b>75%</b> atau <b>50%</b>", tbl_badge_amber),
            Paragraph("<b>KUNCI UTAMA:</b> Menurunkan zoom webview. Untuk TV 55\"–75\", gunakan <b>75%</b>. Jika tepi kartu sholat masih terpotong, gunakan <b>50%</b> hingga pas memenuhi bingkai TV.", tbl_cell_style)
        ],
        [
            Paragraph("Web Content &gt;<br/><b>Enable Viewport Meta Tag</b>", tbl_cell_bold),
            Paragraph("<b>ENABLE (ON)</b>", tbl_badge_green),
            Paragraph("Memerintahkan browser mematuhi skala responsif yang sudah dirancang pada kodingan web masjid.", tbl_cell_style)
        ],
        [
            Paragraph("Web Content &gt;<br/><b>Text Zoom</b>", tbl_cell_bold),
            Paragraph("<b>100%</b>", tbl_badge_blue),
            Paragraph("Mengunci ukuran teks agar tidak dibesarkan secara sepihak oleh sistem Android TV.", tbl_cell_style)
        ],
        [
            Paragraph("Web Browsing &gt;<br/><b>View Mode</b>", tbl_cell_bold),
            Paragraph("<b>Desktop Mode</b>", tbl_badge_green),
            Paragraph("<b>WAJIB:</b> Memastikan TV dirender sebagai layar lebar komputer/TV (16:9), bukan mode mobile HP yang sempit.", tbl_cell_style)
        ]
    ]
    t1 = Table(tbl1_data, colWidths=[48 * mm, 34 * mm, 98 * mm])
    t1.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), colors.HexColor("#065F46")),
        ('GRID', (0,0), (-1,-1), 0.5, colors.HexColor("#CBD5E1")),
        ('ROWBACKGROUNDS', (0,1), (-1,-1), [colors.white, colors.HexColor("#F8FAFC")]),
        ('VALIGN', (0,0), (-1,-1), 'TOP'),
        ('LEFTPADDING', (0,0), (-1,-1), 5),
        ('RIGHTPADDING', (0,0), (-1,-1), 5),
        ('TOPPADDING', (0,0), (-1,-1), 4),
        ('BOTTOMPADDING', (0,0), (-1,-1), 4),
    ]))
    story.append(t1)
    story.append(Spacer(1, 3 * mm))

    # ==========================
    # BAGIAN 2: PENGATURAN CACHE
    # ==========================
    story.append(Paragraph("2. Pengaturan Cache (Agar Data Selalu Realtime dari Cloudflare)", h1_style))
    story.append(Paragraph(
        "Mencegah TV menahan cache file lama sehingga pergantian jadwal sholat, pengumuman, dan petugas Jum'at langsung tayang seketika:",
        body_style
    ))
    story.append(Spacer(1, 2 * mm))

    tbl2_data = [
        [Paragraph("Menu di Fully Kiosk TV", tbl_header_style), Paragraph("Nilai yang Dipilih", tbl_header_style), Paragraph("Fungsi & Catatan Teknis", tbl_header_style)],
        [
            Paragraph("Web Browsing &gt;<br/><b>Webview Cache Mode</b>", tbl_cell_bold),
            Paragraph("<b>LOAD_DEFAULT</b>", tbl_badge_green),
            Paragraph("<b>PENTING:</b> Hindari opsi <i>LOAD_CACHE_ONLY</i> atau <i>LOAD_CACHE_ELSE_NETWORK</i> karena akan membuat TV terus menayangkan data lama.", tbl_cell_style)
        ],
        [
            Paragraph("Web Browsing &gt;<br/><b>Clear Cache on Reload</b>", tbl_cell_bold),
            Paragraph("<b>ENABLE (ON)</b>", tbl_badge_green),
            Paragraph("Setiap kali halaman dimuat ulang, memori cache WebView langsung dibersihkan dan aset ditarik baru dari Cloudflare.", tbl_cell_style)
        ],
        [
            Paragraph("Web Browsing &gt;<br/><b>Enable DOM Storage</b>", tbl_cell_bold),
            Paragraph("<b>ENABLE (ON)</b>", tbl_badge_green),
            Paragraph("Wajib aktif untuk menyimpan preferensi sesi lokal di memori TV.", tbl_cell_style)
        ],
        [
            Paragraph("Web Browsing &gt;<br/><b>Enable Third Party Cookies</b>", tbl_cell_bold),
            Paragraph("<b>ENABLE (ON)</b>", tbl_badge_green),
            Paragraph("Dibutuhkan untuk menjaga kontinuitas koneksi WebSocket Supabase Realtime.", tbl_cell_style)
        ],
        [
            Paragraph("Web Browsing &gt;<br/><b>Clear Cache Now</b>", tbl_cell_bold),
            Paragraph("<b>[ KLIK 1 KALI ]</b>", tbl_badge_amber),
            Paragraph("Klik tombol ini satu kali saat pertama kali mengatur TV untuk membuang seluruh cache usang.", tbl_cell_style)
        ]
    ]
    t2 = Table(tbl2_data, colWidths=[48 * mm, 34 * mm, 98 * mm])
    t2.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), colors.HexColor("#065F46")),
        ('GRID', (0,0), (-1,-1), 0.5, colors.HexColor("#CBD5E1")),
        ('ROWBACKGROUNDS', (0,1), (-1,-1), [colors.white, colors.HexColor("#F8FAFC")]),
        ('VALIGN', (0,0), (-1,-1), 'TOP'),
        ('LEFTPADDING', (0,0), (-1,-1), 5),
        ('RIGHTPADDING', (0,0), (-1,-1), 5),
        ('TOPPADDING', (0,0), (-1,-1), 4),
        ('BOTTOMPADDING', (0,0), (-1,-1), 4),
    ]))
    story.append(t2)

    # PINDAH KE HALAMAN 2
    story.append(PageBreak())

    # ==========================
    # BAGIAN 3: STARTUP & KIOSK MODE
    # ==========================
    story.append(Paragraph("3. Pengaturan Startup & Kiosk Mode (Otomatis & Stabil 24 Jam)", h1_style))
    story.append(Paragraph(
        "Membuat TV beroperasi layaknya media signage profesional: langsung menyala otomatis ke layar masjid tanpa gangguan status bar Android:",
        body_style
    ))
    story.append(Spacer(1, 2 * mm))

    tbl3_data = [
        [Paragraph("Menu di Fully Kiosk TV", tbl_header_style), Paragraph("Nilai yang Dipilih", tbl_header_style), Paragraph("Fungsi & Catatan Teknis", tbl_header_style)],
        [
            Paragraph("Web Content &gt;<br/><b>Start URL</b>", tbl_cell_bold),
            Paragraph("<b>https://digitalaljihad.my.id/</b>", tbl_badge_green),
            Paragraph("Alamat web utama yang langsung dimuat saat TV dinyalakan.", tbl_cell_style)
        ],
        [
            Paragraph("Device Management &gt;<br/><b>Launch on Boot</b>", tbl_cell_bold),
            Paragraph("<b>ENABLE (ON)</b>", tbl_badge_green),
            Paragraph("Ketika TV/TV Box dinyalakan (misal setelah listrik padam), aplikasi otomatis terbuka sendiri.", tbl_cell_style)
        ],
        [
            Paragraph("Device Management &gt;<br/><b>Keep Screen On</b>", tbl_cell_bold),
            Paragraph("<b>ENABLE (ON)</b>", tbl_badge_green),
            Paragraph("Mencegah layar TV masuk mode tidur/sleep/mati saat jam operasional masjid.", tbl_cell_style)
        ],
        [
            Paragraph("Kiosk Mode &gt;<br/><b>Enable Kiosk Mode</b>", tbl_cell_bold),
            Paragraph("<b>ENABLE (ON)</b>", tbl_badge_green),
            Paragraph("Mengunci aplikasi agar jamaah atau anak-anak tidak bisa keluar ke menu Android sembarangan.", tbl_cell_style)
        ],
        [
            Paragraph("Web Browsing &gt;<br/><b>Pull to Refresh</b>", tbl_cell_bold),
            Paragraph("<b>DISABLE (OFF)</b>", tbl_badge_amber),
            Paragraph("Matikan fitur geser ke bawah agar layar tidak bergeser tidak sengaja saat interaksi.", tbl_cell_style)
        ],
        [
            Paragraph("Web Auto Reload &gt;<br/><b>Periodic Web Reload</b>", tbl_cell_bold),
            Paragraph("<b>86400 detik</b>", tbl_badge_blue),
            Paragraph("Otomatis refresh 1 kali per 24 jam (tengah malam) untuk menjaga performa memori TV tetap segar.", tbl_cell_style)
        ],
        [
            Paragraph("Web Auto Reload &gt;<br/><b>Reload on Network Reconnect</b>", tbl_cell_bold),
            Paragraph("<b>ENABLE (ON)</b>", tbl_badge_green),
            Paragraph("Jika Wi-Fi masjid sempat terputus lalu tersambung lagi, TV otomatis reload tanpa perlu disentuh.", tbl_cell_style)
        ]
    ]
    t3 = Table(tbl3_data, colWidths=[48 * mm, 34 * mm, 98 * mm])
    t3.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), colors.HexColor("#065F46")),
        ('GRID', (0,0), (-1,-1), 0.5, colors.HexColor("#CBD5E1")),
        ('ROWBACKGROUNDS', (0,1), (-1,-1), [colors.white, colors.HexColor("#F8FAFC")]),
        ('VALIGN', (0,0), (-1,-1), 'TOP'),
        ('LEFTPADDING', (0,0), (-1,-1), 5),
        ('RIGHTPADDING', (0,0), (-1,-1), 5),
        ('TOPPADDING', (0,0), (-1,-1), 3.5),
        ('BOTTOMPADDING', (0,0), (-1,-1), 3.5),
    ]))
    story.append(t3)
    story.append(Spacer(1, 3 * mm))

    # ==========================
    # BAGIAN 4: REMOTE ADMINISTRATION
    # ==========================
    story.append(Paragraph("4. Fitur Remote Admin dari Meja Sekretariat (Tanpa Remote TV Fisik)", h1_style))
    story.append(Paragraph(
        "Pengurus DKM tidak perlu repot mencari remote TV fisik untuk me-reload layar atau membersihkan cache:",
        body_style
    ))
    story.append(Spacer(1, 2 * mm))

    tbl4_data = [
        [Paragraph("Saluran Remote", tbl_header_style), Paragraph("Pengaturan", tbl_header_style), Paragraph("Cara Penggunaan & Keuntungan", tbl_header_style)],
        [
            Paragraph("<b>Fully Kiosk Remote Admin</b><br/>(Fitur Aplikasi TV)", tbl_cell_bold),
            Paragraph("Enable: <b>ON</b><br/>Port: <b>2323</b><br/>Password: <i>(buat sandi DKM)</i>", tbl_badge_green),
            Paragraph("Buka browser laptop/HP yang satu jaringan Wi-Fi dengan TV, ketik: <b>http://[IP_TV]:2323</b>. Pengurus bisa klik <b>Reload</b>, <b>Clear Cache</b>, atau melihat screenshot TV secara live dari meja kantor.", tbl_cell_style)
        ],
        [
            Paragraph("<b>Web Admin Signage</b><br/>(Fitur Web Bawaan)", tbl_cell_bold),
            Paragraph("Menu: <b>Remote TV</b><br/>URL: <code>admin.html</code>", tbl_badge_blue),
            Paragraph("Di Dashboard Super Admin web, terdapat tombol <b>'Muat Ulang TV (Reload)'</b>. Cukup klik tombol tersebut, sinyal WebSocket akan menyuruh TV reload seketika.", tbl_cell_style)
        ]
    ]
    t4 = Table(tbl4_data, colWidths=[48 * mm, 34 * mm, 98 * mm])
    t4.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), colors.HexColor("#065F46")),
        ('GRID', (0,0), (-1,-1), 0.5, colors.HexColor("#CBD5E1")),
        ('ROWBACKGROUNDS', (0,1), (-1,-1), [colors.white, colors.HexColor("#F8FAFC")]),
        ('VALIGN', (0,0), (-1,-1), 'TOP'),
        ('LEFTPADDING', (0,0), (-1,-1), 5),
        ('RIGHTPADDING', (0,0), (-1,-1), 5),
        ('TOPPADDING', (0,0), (-1,-1), 4),
        ('BOTTOMPADDING', (0,0), (-1,-1), 4),
    ]))
    story.append(t4)
    story.append(Spacer(1, 3 * mm))

    # ==========================
    # BAGIAN 5: CHECKLIST PENGERJAAN
    # ==========================
    story.append(Paragraph("5. Lembar Checklist Pengujian di Depan TV (Waktu: 5 Menit)", h1_style))
    
    chk_data = [
        [
            Paragraph(
                "<b>[ &nbsp; ] 1. Start URL:</b> Tampil logo resmi Masjid Jami' Al-Jihad dengan jam digital detik-per-detik berjalan lancar.<br/>"
                "<b>[ &nbsp; ] 2. Skala Layar:</b> Kartu sholat 5 waktu dan teks berjalan bawah terlihat utuh (tidak terpotong di tepi bingkai TV).<br/>"
                "<b>[ &nbsp; ] 3. Uji Sinkronisasi:</b> Buka <code>https://digitalaljihad.my.id/login.html</code> dari HP pengurus (Login: <b>admin</b> / <b>SuperUser1971</b>). Edit salah satu teks, pastikan TV berubah seketika.<br/>"
                "<b>[ &nbsp; ] 4. Kunci Kiosk:</b> Kunci aplikasi agar TV aman beroperasi otomatis tanpa kendala setiap hari.",
                callout_style
            )
        ]
    ]
    t_chk = Table(chk_data, colWidths=[180 * mm])
    t_chk.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,-1), colors.HexColor("#F1F5F9")),
        ('LINELEFT', (0,0), (-1,-1), 3, colors.HexColor("#0D9488")),
        ('BOX', (0,0), (-1,-1), 0.5, colors.HexColor("#CBD5E1")),
        ('LEFTPADDING', (0,0), (-1,-1), 8),
        ('RIGHTPADDING', (0,0), (-1,-1), 8),
        ('TOPPADDING', (0,0), (-1,-1), 5),
        ('BOTTOMPADDING', (0,0), (-1,-1), 5),
    ]))
    story.append(t_chk)

    doc.build(story, canvasmaker=NumberedCanvas)
    print(f"PDF berhasil dibuat: {output_filename}")

if __name__ == '__main__':
    # Simpan di root workspace
    output_path = os.path.join(os.path.dirname(os.path.abspath(__file__)), 'PANDUAN_FULLY_KIOSK_SMART_TV_ALJIHAD.pdf')
    create_pdf(output_path)
    
    # Salin juga ke web-statis agar bisa diunduh via URL https://digitalaljihad.my.id/PANDUAN_FULLY_KIOSK_SMART_TV_ALJIHAD.pdf
    web_statis_path = os.path.join(os.path.dirname(os.path.abspath(__file__)), 'web-statis', 'PANDUAN_FULLY_KIOSK_SMART_TV_ALJIHAD.pdf')
    try:
        import shutil
        shutil.copy2(output_path, web_statis_path)
        print(f"PDF berhasil disalin ke web-statis: {web_statis_path}")
    except Exception as e:
        print(f"Error menyalin ke web-statis: {e}")
