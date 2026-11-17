export default function readingDescription(description, provider) {
  if (!description) return description;

  let text = description.replace(/\r\n?/g, "\n");

  if (provider === "manga_updates") {
    text = text.replace(/^\s*From\s+[^:\n]{1,80}:\s*/i, "").replace(
      /\*\*Official\s+(?:Japanese|Translations?)\s*:\s*\*\*[\s\S]*?(?=\bNotes?\s*:|$)/gi,
      "\n\n",
    );
  }

  if (provider) {
    text = text
      .replace(/^\s*\[([^\]\n]+)\]:[ \t]*<?https?:\/\/[^\n]*$/gim, "")
      .replace(/^[ \t]*\(?\[(?:source|fuente)\](?:\[[^\]\n]*\]|\(https?:\/\/[^\n)]+\))\)?[ \t]*$/gim, "")
      .replace(/!?\[([^\]\n]*)\]\(https?:\/\/[^\s)]+\)/gi, "$1")
      .replace(/\[([^\]\n]+)\]\[[^\]\n]*\]/g, "$1")
      .replace(/<?https?:\/\/[^\s<>]+>?/gi, "");
  }

  return text
    .replace(/[ \t]+\n/g, "\n")
    .replace(/\n(?:[ \t]*\n)+/g, "\n\n")
    .trim();
}
