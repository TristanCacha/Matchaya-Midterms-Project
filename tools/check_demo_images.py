"""Check the committed image set against its reproducible manifest."""

import json
from pathlib import Path

from PIL import Image


ROOT = Path(__file__).resolve().parents[1]
entries = json.loads((ROOT / 'database/seeds/image-manifest.json').read_text(encoding='utf-8'))
assert len(entries) == 19, f'Expected 17 products and 2 brand images; found {len(entries)}'
assert len({entry['slug'] for entry in entries}) == len(entries)

product_count = 0
public_bytes = 0
matcha_subjects = {
    'usucha', 'koicha', 'hot-matcha-latte', 'iced-matcha-latte',
    'strawberry-matcha', 'sea-salt-cream-matcha', 'banana-pudding-matcha',
    'langka-matcha', 'coffee-matcha', 'matcha-basque-cheesecake',
    'matcha-mochi', 'matcha-tin',
}
for entry in entries:
    assert entry['alt'] and entry['tool'] and entry['license_url']
    assert (ROOT / entry['source_file']).is_file(), entry['source_file']
    if entry['kind'] == 'brand':
        path = ROOT / entry['small_file']
        assert path == ROOT / entry['large_file']
        assert path.stat().st_size == entry['bytes']['display']
        with Image.open(path) as image:
            assert image.format == 'WEBP'
            assert list(image.size) == entry['dimensions']['display']
        public_bytes += path.stat().st_size
        continue
    for size in ('small', 'large'):
        if f'{size}_file' not in entry:
            continue
        path = ROOT / entry[f'{size}_file']
        assert path.is_file(), str(path)
        assert path.stat().st_size == entry['bytes'][size], str(path)
        public_bytes += path.stat().st_size
        with Image.open(path) as image:
            assert image.format == 'WEBP', str(path)
            assert list(image.size) == entry['dimensions'][size], str(path)
            if entry['kind'] == 'product':
                assert image.width == image.height, str(path)
            if size == 'small' and entry['kind'] == 'product':
                pixels = list(image.convert('HSV').resize((80, 80)).get_flattened_data())
                green_fraction = sum(26 <= h <= 105 and s > 40 and v > 35 for h, s, v in pixels) / len(pixels)
                if entry['slug'] in matcha_subjects:
                    assert green_fraction >= .06, f"{entry['slug']} lacks visible green: {green_fraction:.2f}"
                if entry['slug'].startswith('hojicha'):
                    assert green_fraction < .03, f"{entry['slug']} looks green: {green_fraction:.2f}"
        if entry['kind'] == 'product' and size == 'small':
            assert path.stat().st_size < 80 * 1024, str(path)
    if entry['kind'] == 'product':
        product_count += 1

assert product_count == 17, product_count
print(f'PASS: {product_count} distinct product images, 2 brand images, {public_bytes:,} public bytes')
