import os
import math
from PIL import Image, ImageDraw, ImageFont, ImageFilter

output_dir = r"d:\nafi\coding\eventix\assets\uploads\posters"
os.makedirs(output_dir, exist_ok=True)

posters_data = [
    {
        "filename": "seventeen.jpg",
        "artist": "SEVENTEEN",
        "tour": "[RIGHT HERE] WORLD TOUR",
        "genre": "K-POP / PERFORMANCE",
        "date": "12 DES 2026",
        "venue": "JIS, Jakarta",
        "c1": (247, 180, 195),  # Rose Quartz
        "c2": (40, 50, 90),     # Serenity Navy
        "accent": (255, 96, 130)
    },
    {
        "filename": "iu.jpg",
        "artist": "IU",
        "tour": "H.E.R. WORLD TOUR CONCERT",
        "genre": "K-POP / BALLAD",
        "date": "19 DES 2026",
        "venue": "ICE BSD Hall 5-6",
        "c1": (180, 140, 215),  # Lilac
        "c2": (35, 20, 60),     # Deep Violet
        "accent": (230, 190, 255)
    },
    {
        "filename": "day6.jpg",
        "artist": "DAY6",
        "tour": "<FOREVER YOUNG> 3RD WORLD TOUR",
        "genre": "K-ROCK / POP ROCK",
        "date": "9 JAN 2027",
        "venue": "Stadion Madya GBK",
        "c1": (255, 120, 40),   # Sunset Orange
        "c2": (20, 25, 65),     # Deep Indigo
        "accent": (255, 180, 50)
    },
    {
        "filename": "blackpink.jpg",
        "artist": "BLACKPINK",
        "tour": "[BORN PINK] WORLD TOUR",
        "genre": "K-POP / HIP-HOP",
        "date": "23 JAN 2027",
        "venue": "SUGBK, Jakarta",
        "c1": (255, 20, 147),   # Deep Pink
        "c2": (15, 15, 20),     # Obsidian Black
        "accent": (255, 105, 180)
    },
    {
        "filename": "wave_to_earth.jpg",
        "artist": "wave to earth",
        "tour": "0.03 WORLD TOUR",
        "genre": "K-INDIE / LO-FI",
        "date": "6 FEB 2027",
        "venue": "Istora Senayan",
        "c1": (120, 140, 110),  # Vintage Olive
        "c2": (30, 38, 28),     # Earthy Dark
        "accent": (200, 215, 180)
    },
    {
        "filename": "aespa.jpg",
        "artist": "aespa",
        "tour": "SYNK : PARALLEL LINE",
        "genre": "K-POP / HYPERPOP",
        "date": "20 FEB 2027",
        "venue": "BCIS, Ancol",
        "c1": (6, 182, 212),    # Cyber Teal
        "c2": (88, 28, 135),    # Hyper Purple
        "accent": (168, 85, 247)
    },
    {
        "filename": "nct_dream.jpg",
        "artist": "NCT DREAM",
        "tour": "THE DREAM SHOW 3 : DREAM( )SCAPE",
        "genre": "K-POP / DANCE POP",
        "date": "6 MAR 2027",
        "venue": "SUGBK, Jakarta",
        "c1": (132, 204, 22),   # Lime Pearl
        "c2": (10, 40, 30),     # Deep Forest
        "accent": (190, 242, 100)
    },
    {
        "filename": "dpr.jpg",
        "artist": "DPR (IAN, CREAM, ARTIC)",
        "tour": "THE DREAM REBORN WORLD TOUR",
        "genre": "K-R&B / ALTERNATIVE",
        "date": "20 MAR 2027",
        "venue": "BCIS, Ancol",
        "c1": (220, 38, 38),    # Cinematic Crimson
        "c2": (15, 20, 35),     # Moody Noir
        "accent": (248, 113, 113)
    },
    {
        "filename": "twice.jpg",
        "artist": "TWICE",
        "tour": "5TH WORLD TOUR 'READY TO BE'",
        "genre": "K-POP / POP DANCE",
        "date": "10 APR 2027",
        "venue": "JIS, Jakarta",
        "c1": (255, 94, 126),   # Apricot Magenta
        "c2": (50, 20, 55),     # Magenta Dark
        "accent": (255, 170, 166)
    },
    {
        "filename": "stray_kids.jpg",
        "artist": "Stray Kids",
        "tour": "<dominATE> WORLD TOUR",
        "genre": "K-POP / RAP ROCK",
        "date": "24 APR 2027",
        "venue": "Indonesia Arena GBK",
        "c1": (239, 68, 68),    # Industrial Red
        "c2": (18, 18, 22),     # Steel Dark
        "accent": (250, 204, 21)
    },
    {
        "filename": "taeyeon.jpg",
        "artist": "TAEYEON",
        "tour": "THE ODD OF LOVE CONCERT",
        "genre": "K-POP / VOCAL",
        "date": "8 MEI 2027",
        "venue": "ICE BSD Hall 5-6",
        "c1": (244, 63, 94),    # Rose Gold
        "c2": (23, 37, 84),     # Midnight Indigo
        "accent": (251, 146, 60)
    },
    {
        "filename": "baekhyun.jpg",
        "artist": "BAEKHYUN",
        "tour": "ASIA TOUR [Lonsdaleite]",
        "genre": "K-POP / R&B",
        "date": "22 MEI 2027",
        "venue": "ICE BSD Hall 5",
        "c1": (59, 130, 246),   # Electric Blue
        "c2": (20, 15, 45),     # Deep Night
        "accent": (147, 197, 253)
    }
]

W, H = 1200, 750

try:
    font_title = ImageFont.truetype("C:/Windows/Fonts/segoeuib.ttf", 64)
    font_sub = ImageFont.truetype("C:/Windows/Fonts/segoeuib.ttf", 30)
    font_badge = ImageFont.truetype("C:/Windows/Fonts/segoeuib.ttf", 22)
    font_meta = ImageFont.truetype("C:/Windows/Fonts/arial.ttf", 24)
except Exception:
    font_title = ImageFont.load_default()
    font_sub = ImageFont.load_default()
    font_badge = ImageFont.load_default()
    font_meta = ImageFont.load_default()

for p in posters_data:
    img = Image.new("RGB", (W, H))
    draw = ImageDraw.Draw(img)
    
    # 1. Base gradient
    c1 = p["c1"]
    c2 = p["c2"]
    for y in range(H):
        ratio = y / H
        # non-linear curve for drama
        curve = 0.5 * (1 - math.cos(ratio * math.pi))
        r = int(c2[0] * (1 - curve) + c1[0] * curve * 0.4)
        g = int(c2[1] * (1 - curve) + c1[1] * curve * 0.4)
        b = int(c2[2] * (1 - curve) + c1[2] * curve * 0.4)
        draw.line([(0, y), (W, y)], fill=(r, g, b))
        
    # 2. Glowing spotlight circles overlay
    glow = Image.new("RGBA", (W, H), (0, 0, 0, 0))
    gdraw = ImageDraw.Draw(glow)
    
    # Big radial spotlight top center
    cx, cy = W // 2, 100
    for rad in range(450, 50, -40):
        alpha = int((1 - rad / 450) * 110)
        gdraw.ellipse([cx - rad, cy - rad, cx + rad, cy + rad], fill=(c1[0], c1[1], c1[2], alpha))
        
    # Secondary corner accent glow
    for rad in range(300, 40, -30):
        alpha = int((1 - rad / 300) * 80)
        gdraw.ellipse([W - 150 - rad, H - 100 - rad, W - 150 + rad, H - 100 + rad], fill=(p["accent"][0], p["accent"][1], p["accent"][2], alpha))
        
    # Geometric concert lines
    for i in range(-5, 6):
        x_start = cx + i * 90
        gdraw.line([(cx, cy - 50), (x_start, H)], fill=(255, 255, 255, 18), width=2)
        
    glow = glow.filter(ImageFilter.GaussianBlur(15))
    img.paste(glow, (0, 0), glow)
    
    # Re-wrap draw
    draw = ImageDraw.Draw(img)
    
    # 3. Top EVENTIX Badge
    draw.rounded_rectangle([60, 50, 240, 95], radius=12, fill=(15, 23, 42, 220), outline=(255, 255, 255, 80), width=1)
    draw.text((80, 60), "EVENTIX LIVE", font=font_badge, fill=(255, 96, 130))
    
    # Genre Pill
    draw.rounded_rectangle([W - 380, 50, W - 60, 95], radius=12, fill=(15, 23, 42, 220), outline=(255, 255, 255, 80), width=1)
    draw.text((W - 360, 60), p["genre"], font=font_badge, fill=(240, 240, 250))
    
    # 4. Center Typography
    # Artist Name
    artist_bbox = draw.textbbox((0, 0), p["artist"], font=font_title)
    art_w = artist_bbox[2] - artist_bbox[0]
    art_x = (W - art_w) // 2
    art_y = 280
    
    # Artist Shadow
    draw.text((art_x + 3, art_y + 4), p["artist"], font=font_title, fill=(0, 0, 0, 220))
    draw.text((art_x, art_y), p["artist"], font=font_title, fill=(255, 255, 255))
    
    # Tour Name
    tour_bbox = draw.textbbox((0, 0), p["tour"], font=font_sub)
    tour_w = tour_bbox[2] - tour_bbox[0]
    tour_x = (W - tour_w) // 2
    tour_y = art_y + 90
    draw.text((tour_x + 2, tour_y + 2), p["tour"], font=font_sub, fill=(0, 0, 0, 200))
    draw.text((tour_x, tour_y), p["tour"], font=font_sub, fill=p["accent"])
    
    # Horizontal Divider Line
    draw.line([(W // 2 - 180, tour_y + 65), (W // 2 + 180, tour_y + 65)], fill=(255, 255, 255, 120), width=2)
    
    # Date & Venue at Bottom
    meta_text = f"{p['date']}   •   {p['venue']}"
    meta_bbox = draw.textbbox((0, 0), meta_text, font=font_meta)
    meta_w = meta_bbox[2] - meta_bbox[0]
    meta_x = (W - meta_w) // 2
    meta_y = tour_y + 90
    
    draw.rounded_rectangle([meta_x - 30, meta_y - 12, meta_x + meta_w + 30, meta_y + 42], radius=16, fill=(15, 23, 42, 230), outline=(255, 96, 130), width=2)
    draw.text((meta_x, meta_y), meta_text, font=font_meta, fill=(255, 255, 255))

    # Save JPG
    target_path = os.path.join(output_dir, p["filename"])
    img.save(target_path, "JPEG", quality=92)
    print(f"Generated: {p['filename']}")

print("All 12 posters generated successfully!")
