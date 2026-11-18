import CollectionInformation from "../../../components/CollectionInformation/CollectionInformation";
import readingDescription from "../../../utils/readingDescription";
import ReadingCover from "../ReadingCover/ReadingCover";

const sources = {
  comic_vine: {
    label: "Comic Vine",
    linkLabel: "Ver en Comic Vine",
    url: (id) => `https://comicvine.gamespot.com/issue/4000-${id}/`,
  },
  manga_updates: {
    label: "MangaUpdates",
    linkLabel: "MangaUpdates",
    url: () => "https://www.mangaupdates.com/",
  },
  open_library: {
    label: "Open Library",
    linkLabel: "Ver en Open Library",
    url: (id) => `https://openlibrary.org/works/${id}`,
  },
};

export default function ReadingInformation({ reading }) {
  const { title, author, publication_date, cover_url, provider, external_id } = reading;
  const source = Object.hasOwn(sources, provider) ? sources[provider] : null;
  const id = external_id?.trim();

  return (
    <CollectionInformation
      poster={<ReadingCover title={title} url={cover_url} eager />}
      synopsis={readingDescription(reading.description, provider)}
    >
      {author && <><dt>Autoría</dt><dd>{author}</dd></>}
      {publication_date && (
        <><dt>Publicación</dt><dd><time dateTime={publication_date}>{publication_date}</time></dd></>
      )}
      <dt>Ficha</dt>
      <dd>
        {source && id ? (
          <a href={source.url(id)}>{source.linkLabel}</a>
        ) : (
          source?.label ?? provider ?? "Añadida a mano"
        )}
      </dd>
    </CollectionInformation>
  );
}
