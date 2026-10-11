import os
from PIL import Image

def optimize_image(filepath, max_width=1920):
    if not os.path.exists(filepath):
        print(f"Skipping (not found): {filepath}")
        return

    orig_size = os.path.getsize(filepath) / 1024
    img = Image.open(filepath)
    w, h = img.size
    orig_mode = img.mode

    # Resize if oversized
    if w > max_width:
        new_h = int(h * (max_width / w))
        img = img.resize((max_width, new_h), Image.Resampling.LANCZOS)
        print(f"  Resized {os.path.basename(filepath)}: {w}x{h} -> {max_width}x{new_h}")
    else:
        print(f"  Processing {os.path.basename(filepath)}: {w}x{h}")

    base, ext = os.path.splitext(filepath)
    ext_lower = ext.lower()

    # 1. Generate WebP version
    webp_path = base + ".webp"
    # Ensure RGB or RGBA for WebP
    if img.mode in ('RGBA', 'LA') or (img.mode == 'P' and 'transparency' in img.info):
        img_webp = img.convert('RGBA')
    else:
        img_webp = img.convert('RGB')
    
    img_webp.save(webp_path, 'WEBP', quality=84, method=6)
    webp_size = os.path.getsize(webp_path) / 1024
    print(f"    -> Created {os.path.basename(webp_path)}: {webp_size:.1f} KB (orig: {orig_size:.1f} KB, saved: {(1 - webp_size/orig_size)*100:.1f}%)")

    # 2. Optimize original file format in-place for backwards compatibility
    if ext_lower in ('.jpg', '.jpeg'):
        img_rgb = img.convert('RGB')
        img_rgb.save(filepath, 'JPEG', quality=84, progressive=True, optimize=True)
        new_size = os.path.getsize(filepath) / 1024
        print(f"    -> Optimized JPG {os.path.basename(filepath)}: {new_size:.1f} KB (saved: {(1 - new_size/orig_size)*100:.1f}%)")
    elif ext_lower == '.png':
        # If image has no transparency, optimize as RGB PNG or 256-color paletted if beneficial
        if img.mode in ('RGBA', 'LA') or (img.mode == 'P' and 'transparency' in img.info):
            img.save(filepath, 'PNG', optimize=True)
        else:
            img_rgb = img.convert('RGB')
            # Check quantize vs direct PNG optimize
            img_quant = img_rgb.quantize(colors=256, method=Image.Resampling.LANCZOS)
            test_quant = filepath + ".test_q.png"
            img_quant.save(test_quant, 'PNG', optimize=True)
            q_size = os.path.getsize(test_quant) / 1024

            test_rgb = filepath + ".test_rgb.png"
            img_rgb.save(test_rgb, 'PNG', optimize=True)
            r_size = os.path.getsize(test_rgb) / 1024

            if q_size < r_size and q_size < orig_size:
                os.replace(test_quant, filepath)
                if os.path.exists(test_rgb): os.remove(test_rgb)
            else:
                os.replace(test_rgb, filepath)
                if os.path.exists(test_quant): os.remove(test_quant)
                
        new_size = os.path.getsize(filepath) / 1024
        print(f"    -> Optimized PNG {os.path.basename(filepath)}: {new_size:.1f} KB (saved: {(1 - new_size/orig_size)*100:.1f}%)")

def main():
    bg_dir = "web-statis/image/display/background"
    img_dir = "web-statis/img"

    bg_files = [os.path.join(bg_dir, f) for f in os.listdir(bg_dir) if f.lower().endswith(('.png', '.jpg', '.jpeg'))]
    extra_files = [
        os.path.join(img_dir, "Kaabah.png"),
        os.path.join(img_dir, "Kaabah222222.png"),
        os.path.join(img_dir, "kaabah111.png"),
        os.path.join(img_dir, "bg-flyer-jumat.jpg"),
        os.path.join(img_dir, "login-bg.jpg")
    ]

    all_targets = bg_files + [f for f in extra_files if os.path.exists(f)]
    print(f"Found {len(all_targets)} background images to optimize:")

    total_orig = sum(os.path.getsize(f)/1024 for f in all_targets)

    for f in all_targets:
        optimize_image(f)

    total_webp = sum(os.path.getsize(os.path.splitext(f)[0] + ".webp")/1024 for f in all_targets if os.path.exists(os.path.splitext(f)[0] + ".webp"))
    total_new = sum(os.path.getsize(f)/1024 for f in all_targets)

    print("\n" + "="*50)
    print(f"SUMMARY OF BACKGROUND OPTIMIZATION:")
    print(f"Total Original Size : {total_orig:.1f} KB ({total_orig/1024:.2f} MB)")
    print(f"Total Optimized In-Place : {total_new:.1f} KB ({total_new/1024:.2f} MB) - Reduction: {(1 - total_new/total_orig)*100:.1f}%")
    print(f"Total WebP Size     : {total_webp:.1f} KB ({total_webp/1024:.2f} MB) - Reduction: {(1 - total_webp/total_orig)*100:.1f}%")
    print("="*50)

if __name__ == "__main__":
    main()
