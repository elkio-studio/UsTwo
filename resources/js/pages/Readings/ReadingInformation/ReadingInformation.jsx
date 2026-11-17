import CollectionInformation from "../../../components/CollectionInformation/CollectionInformation";
import readingDescription from "../../../utils/readingDescription";
import ReadingCover from "../ReadingCover/ReadingCover";

export default function ReadingInformation({ reading }) {
  const { title, author, publication_date, cover_url, provider, external_id } = reading;

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
        {provider === "comic_vine" && <a href={`https://comicvine.gamespot.com/issue/4000-${external_id}/`}>Ver en Comic Vine</a>}
        {provider === "manga_updates" && <a href="https://www.mangaupdates.com/">MangaUpdates</a>}
        {provider === "open_library" && <a href={`https://openlibrary.org/works/${external_id}`}>Ver en Open Library</a>}
        {!provider && "Añadida a mano"}
      </dd>
    </CollectionInformation>
  );
}
