const PALETTES = [
  ['#163a3c', '#2a5558'],
  ['#1a4540', '#3d6b5c'],
  ['#1e3a4a', '#3a6278'],
  ['#2c3e2d', '#4a6a4a'],
  ['#3d2c29', '#7a5540'],
  ['#243a4a', '#4a6478'],
  ['#2a4038', '#5a7868'],
  ['#1a383c', '#3a6870'],
]

const BLOCKED_HOSTS = [
  'xinsiketang.com',
  'doubanio.com',
  'douban.com',
]

function hashString(str) {
  let h = 0
  const s = String(str || '')
  for (let i = 0; i < s.length; i += 1) {
    h = (h << 5) - h + s.charCodeAt(i)
    h |= 0
  }
  return Math.abs(h)
}

export function isBlockedCoverUrl(url) {
  if (!url || typeof url !== 'string') return true
  const lower = url.toLowerCase()
  return BLOCKED_HOSTS.some((host) => lower.includes(host))
}

export function generateBookCover({ title = '', author = '', id = 0 } = {}) {
  const seed = hashString(`${id}-${title}-${author}`)
  const [c1, c2] = PALETTES[seed % PALETTES.length]
  const shortTitle = String(title || '宇春书城').slice(0, 12)
  const shortAuthor = String(author || '').slice(0, 10)
  const initial = shortTitle.charAt(0) || '书'

  const svg = `<?xml version="1.0" encoding="UTF-8"?>
<svg xmlns="http://www.w3.org/2000/svg" width="300" height="420" viewBox="0 0 300 420">
  <defs>
    <linearGradient id="g" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="${c1}"/>
      <stop offset="100%" stop-color="${c2}"/>
    </linearGradient>
  </defs>
  <rect width="300" height="420" fill="url(#g)"/>
  <rect x="18" y="18" width="264" height="384" fill="none" stroke="rgba(255,255,255,0.28)" stroke-width="1.5"/>
  <text x="150" y="150" text-anchor="middle" fill="rgba(255,255,255,0.2)" font-size="96" font-family="Georgia, serif">${escapeXml(initial)}</text>
  <text x="150" y="250" text-anchor="middle" fill="#fff" font-size="22" font-family="Georgia, serif">${escapeXml(shortTitle)}</text>
  <text x="150" y="290" text-anchor="middle" fill="rgba(255,255,255,0.75)" font-size="14" font-family="Georgia, serif">${escapeXml(shortAuthor)}</text>
  <text x="150" y="380" text-anchor="middle" fill="rgba(255,255,255,0.45)" font-size="12" font-family="Georgia, serif">宇春书城</text>
</svg>`

  return `data:image/svg+xml;charset=utf-8,${encodeURIComponent(svg)}`
}

function escapeXml(text) {
  return String(text)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&apos;')
}

/** Resolve display cover: keep safe custom URLs, otherwise generate placeholder. */
export function resolveBookCover(book = {}) {
  const cover = book.cover
  if (cover && !isBlockedCoverUrl(cover) && !cover.startsWith('data:')) {
    return cover
  }
  if (cover && cover.startsWith('data:image/svg')) {
    return cover
  }
  return generateBookCover({
    title: book.title,
    author: book.author,
    id: book.id,
  })
}
