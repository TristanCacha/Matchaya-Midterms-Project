# Matchaya interface design

The counter uses a quiet neutral surface, dark readable text, and a single fresh green fill for primary action. It is designed for a landscape tablet during a rush, then adapts to a portrait tablet and a phone. The v3 visual pass added one consistent generated product photo shoot, a split sign-in composition, an original bowl-shaped SVG mark and wordmark, an hourly area chart, sparklines, and a thermal-style receipt.

## Tokens and type

| Token | Value | Use |
|---|---|---|
| Ink | `#1B2A19` | Main text |
| Foam | `#F2F5EE` | Page background |
| Mist | `#E3E9DC` | Raised surfaces |
| Fresh | `#7CB82F` | Primary action and selected stamp fills, always with ink text |
| Shade | `#2F5A1E` | Links, focus, pressed state |
| Glaze | `#566669` | Secondary text and control borders |
| Adzuki | `#9A2E2E` | Error, destructive, and low stock text |

The display face is a self-hosted Latin subset of Shippori Mincho; the UI uses self-hosted Zen Kaku Gothic New. Both are licensed under SIL OFL in `public/assets/fonts/`. Their supplied subsets lack the peso glyph, so Arial supplies `₱` as a fallback. Amounts use tabular numerals for stable columns.

## Layout wireframes

Landscape tablet, about 1280 px:

```text
┌──────────────┬───────────────────────────────────────┬───────────────────┐
│ Navigation   │ Menu                                  │ Current order     │
│ Counter      │ Search                                │ Line 1   - 1 +   │
│ Dashboard    │ [All] [Straight] [Milk] [Food]         │ Line 2   - 2 +   │
│ Products     │ [image][image][image]                  │ Customer          │
│ Customers    │ [name ][name ][name ]                  │ Walk-in           │
│ Sales        │ [price][price][price]                  │                   │
│ Staff        │ [image][image][image]                  │ Total             │
│              │ [name ][name ][name ]                  │ [Charge ₱385.00]  │
└──────────────┴───────────────────────────────────────┴───────────────────┘
```

Dashboard:

```text
┌────────────────────────────────────────────────────────────────────────────┐
│ Revenue          Orders          Items sold          Average ticket       │
├────────────────────────────────────────┬───────────────────────────────────┤
│ Orders by hour                        │ Top sellers                       │
├────────────────────────────────────────┼───────────────────────────────────┤
│ Inventory runway                      │ Customer patterns                 │
└────────────────────────────────────────┴───────────────────────────────────┘
```

On portrait tablets, the menu stays above the cart. On phones down to 360 px, product tiles use two columns and the cart opens as a bottom sheet above fixed navigation. The charge action stays at the bottom of the cart panel. Buttons and inputs are at least 44 px high; primary charge is at least 56 px.

## Critique and revision

The first direction inherited the task application's dark green panels, decorative orbs, and a broad hero area. Those made the screen feel like a dashboard used occasionally, so they were removed. The replacement puts products and current order first, uses plain section headings, and saves green for an intentional charge or selected state. The v2 category illustrations looked generic and could imply the wrong drink; v3 replaces them with individual local WebP depictions. The one asymmetry is the narrower cart column beside a denser grid.

The final pass removed the header decoration and a decorative receipt zigzag. Focus, stock, and error states retain text or shape cues, so meaning does not depend on color alone. Motion is confined to a brief tile press and disabled when reduced motion is requested. A live 360 px check also found that a desktop table-row height clipped product cards; the phone override was corrected and deployed.

The generated images use a three-quarter camera angle, soft upper-left light, a warm pale-stone ground, and generous square crops. This avoids interchangeable product art: the image is used where it identifies a menu item or introduces the shop on sign-in. The final visual pass removed the former category SVG art from menu tiles.
