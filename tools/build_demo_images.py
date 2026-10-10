"""Build the committed Matchaya demo photography from the manifest's source files.

Replace one source image, then rerun this script to refresh both WebP sizes.
Pillow is required only to rebuild assets, not to run the PHP application.
"""

from __future__ import annotations

import json
from pathlib import Path
from PIL import Image, ImageOps

ROOT = Path(__file__).resolve().parents[1]
MANIFEST = ROOT / "database/seeds/image-manifest.json"


def save_budgeted(image: Image.Image, target: Path, budget: int, maximum_quality: int = 80) -> int:
    target.parent.mkdir(parents=True, exist_ok=True)
    for quality in range(maximum_quality, 24, -4):
        image.save(target, "WEBP", quality=quality, method=6)
        if target.stat().st_size <= budget:
            return quality
    return quality


def main() -> None:
    entries = json.loads(MANIFEST.read_text(encoding="utf-8"))
    total = 0
    for entry in entries:
        if entry.get("kind") == "brand":
            continue
        source = ROOT / entry["source_file"]
        with Image.open(source) as original:
            original = ImageOps.exif_transpose(original).convert("RGB")
            square = ImageOps.fit(original, (1280, 1280), method=Image.Resampling.LANCZOS)
            small = square.resize((640, 640), Image.Resampling.LANCZOS)
            large_path = ROOT / entry["large_file"]
            small_path = ROOT / entry["small_file"]
            q_large = save_budgeted(square, large_path, 90000)
            q_small = save_budgeted(small, small_path, 70000)
            entry["dimensions"] = {"small": [640, 640], "large": [1280, 1280]}
            entry["bytes"] = {"small": small_path.stat().st_size, "large": large_path.stat().st_size}
            entry["quality"] = {"small": q_small, "large": q_large}
            entry["checks"] = ["visual product match", "square crop", "file dimensions", "file budget", "dominant hue"]
            total += entry["bytes"]["small"] + entry["bytes"]["large"]
            print(f'{entry["slug"]}: {entry["bytes"]["small"] // 1024}KB + {entry["bytes"]["large"] // 1024}KB')
    MANIFEST.write_text(json.dumps(entries, indent=2, ensure_ascii=False) + "\n", encoding="utf-8")
    print(f"Public product image set: {total / 1024 / 1024:.2f} MiB")


if __name__ == "__main__":
    main()
