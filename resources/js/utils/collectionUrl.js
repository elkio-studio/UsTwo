export default function collectionUrl(spaceId, series) {
  return `/espacios/${spaceId}/${series ? "series" : "peliculas"}`;
}
