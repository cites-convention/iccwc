<?php

namespace Drupal\iccwc\Commands;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\file\Entity\File;
use Drupal\media\Entity\Media;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;
use Drush\Commands\DrushCommands;
use GuzzleHttp\ClientInterface;

/**
 * The "IccwcMigrateCommands" class.
 */
class IccwcMigrateCommands extends DrushCommands {

  /**
   * Mapping old news URLs to new ones.
   *
   * @var array
   */
  protected $urlMapping = [
    '/eng/prog/iccwc_new.php' => '',
    '/fra/prog/iccwc_new.php' => '',
    '/esp/prog/iccwc_new.php' => '',
    '/eng/prog/ICCWC.php' => '',
    '/fra/prog/ICCWC.php' => '',
    '/esp/prog/ICCWC.php' => '',
    '/eng/prog/iccwc.php' => '',
    '/fra/prog/iccwc.php' => '',
    '/esp/prog/iccwc.php' => '',

    '/eng/prog/iccwc.php/Partners' => '/partners-and-donors',
    '/fra/prog/iccwc.php/Partners' => '/fr/partners-and-donors',
    '/esp/prog/iccwc.php/Partners' => '/es/partners-and-donors',

    '/eng/prog/iccwc/donors.php' => '/partners-and-donors',
    '/fra/prog/iccwc/donors.php' => '/fr/partners-and-donors',
    '/esp/prog/iccwc/donors.php' => '/es/partners-and-donors',

    '/eng/prog/iccwc.php/Donors' => '/partners-and-donors',
    '/fra/prog/iccwc.php/Donors' => '/fr/partners-and-donors',
    '/esp/prog/iccwc.php/Donors' => '/es/partners-and-donors',

    '/eng/prog/iccwc/goals.php' => '/our-approach',
    '/fra/prog/iccwc/goals.php' => '/fr/our-approach',
    '/esp/prog/iccwc/goals.php' => '/es/our-approach',

    '/eng/prog/iccwc.php/Strategy' => '/our-approach',
    '/fra/prog/iccwc.php/Strategy' => '/fr/our-approach',
    '/esp/prog/iccwc.php/Strategy' => '/esp/our-approach',

    '/esp/prog/iccwc/crime.php' => '',
    '/fra/prog/iccwc/crime.php' => '',
    '/eng/prog/iccwc/crime.php' => '',
    '/esp/prog/iccwc.php/Wildlife-Crime' => '',
    '/fra/prog/iccwc.php/Wildlife-Crime' => '',
    '/eng/prog/iccwc.php/Wildlife-Crime' => '',

    '/eng/prog/iccwc.php/Tools' => '/tools-and-services',
    '/fra/prog/iccwc.php/Tools' => '/fr/tools-and-services',
    '/esp/prog/iccwc.php/Tools' => '/es/tools-and-services',

    '/eng/prog/iccwc/mou.php' => '/letter-of-understanding',
    '/fra/prog/iccwc/mou.php' => '/fr/letter-of-understanding',
    '/esp/prog/iccwc/mou.php' => '/es/letter-of-understanding',

    '/eng/prog/iccwc.php/Action' => '/success-stories',
    '/fra/prog/iccwc.php/Action' => '/fr/success-stories',
    '/esp/prog/iccwc.php/Action' => '/es/success-stories',
    '/eng/prog/iccwc/action.php' => '/success-stories',
    '/fra/prog/iccwc/action.php' => '/fr/success-stories',
    '/esp/prog/iccwc/action.php' => '/es/success-stories',
  ];

  use StringTranslationTrait;

  /**
   * The "GuzzleHttp" service.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $client;

  /**
   * The "FileSystem" service.
   *
   * @var \Drupal\Core\File\FileSystemInterface
   */
  protected $fileSystem;

  /**
   * The "EntityTypeManager" service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  protected $replacedLinks;

  /**
   * {@inheritdoc}
   */
  public function __construct(ClientInterface $client, FileSystemInterface $file_system, EntityTypeManagerInterface $entity_type_manager) {
    $this->client = $client;
    $this->fileSystem = $file_system;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * Migrate news from JSON.
   *
   * @param string $json_path
   *   Path to the JSON file containing the news.
   * @param false[] $options
   *   Command options.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   * @throws \GuzzleHttp\Exception\GuzzleException
   *
   * @command import:news
   * @aliases im-news
   * @options delete-existing Whether or not to delete existing news
   * @usage import:news news.json
   *   Migrate news from news.json.
   */
  public function migrateNews(string $json_path, array $options = ['delete-existing' => FALSE]) {
    if ($options['delete-existing']) {
      $existing_news = $this->entityTypeManager
        ->getStorage('node')
        ->loadByProperties(['type' => 'news']);

      if (count($existing_news)) {
        $this->entityTypeManager
          ->getStorage('node')
          ->delete($existing_news);

        $this->logger->info(
          $this->t('Successfully deleted @count existing news', [
            '@count' => count($existing_news),
          ])
        );
      }
    }

    $file = realpath($json_path);
    if (empty($file)) {
      $this->logger->error('Invalid path to file');
      return;
    }

    // @todo Replace with Drupal API.
    $data = json_decode(file_get_contents($file), TRUE);

    /** @var \Drupal\pathauto\AliasCleanerInterface $alias_cleaner */
    $alias_cleaner = \Drupal::service('pathauto.alias_cleaner');

    foreach ($data as $row) {
      $old_url = $row['url'];
      $old_url = str_replace('/eng/', '', $old_url);

      $title = $row['title'];
      $title = $alias_cleaner->cleanString($title);
      $new_url = '/news/' . $title;
      $new_url = $alias_cleaner->cleanAlias($new_url);

      $this->urlMapping[$old_url] = $new_url;
      $this->urlMapping["/eng$old_url"] = $new_url;
      $this->urlMapping["/fra$old_url"] = "/fr$new_url";
      $this->urlMapping["/esp$old_url"] = "/es$new_url";
    }

    foreach ($data as $row) {
      $this->migrateRow($row, 'news');
    }

    foreach ($this->replacedLinks as $old => $new) {
      echo "\"$old\",\"$new\"\n";
    }
  }

  /**
   * Get a term by its name and vid.
   *
   * @param string $name
   *   The term name.
   * @param string $vid
   *   The vocabulary ID.
   * @param bool $create
   *   If true and the term does not exist, create it.
   *
   * @return \Drupal\taxonomy\TermInterface
   *   The term.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function getTermByName(string $name, string $vid, $create = FALSE) {
    if ($name == 'News and highlights') {
      $name = 'News & Highlights';
    }

    $term = $this->entityTypeManager->getStorage('taxonomy_term')->loadByProperties([
      'vid' => $vid,
      'name' => $name,
    ]);

    if (empty($term)) {
      if (!$create) {
        return FALSE;
      }

      $term = Term::create([
        'vid' => $vid,
        'name' => $name,
      ]);
      $term->save();
      return $term;
    }

    $term = reset($term);
    return $term;
  }

  /**
   * Migrate a news row.
   *
   * @param array $row
   *   A data row.
   * @param string $content_type
   *   The type of content to be imported.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  protected function migrateRow(array $row, $content_type) {
    $body = $row['body'] ?? '';
    $body = $this->prepareMarkup($body);

    $image = $this->getImage($body);

    $this->logger->info(sprintf('Importing report %s', $row['title']));

    $query = $this->entityTypeManager
      ->getStorage('node')
      ->getQuery();

    $query->condition('type', $content_type);
    $query->condition('title', $row['title']);
    $existing_node = $query->execute();

    $tags = $row['tags'];
    foreach ($tags as &$tag) {
      $term = $this->getTermByName($tag, 'tags', TRUE);
      $tag = $term->id();
    }

    $values = [
      'type' => $content_type,
      'title' => $row['title'],
      'created' => $row['created'],
      'changed' => $row['updated'],
      'body' => [
        'value' => $body,
        'format' => 'full_html',
      ],
      'field_tags' => $tags,
    ];

    if (!empty($image)) {
      $values += ['field_image' => $image];
    }

    if (!empty($existing_node)) {
      $existing_node = reset($existing_node);
      $existing_node->delete();
    }
    else {
      $node = Node::create($values);
    }

    foreach (['fr', 'es'] as $langcode) {
      if (empty($row['translations'][$langcode])) {
        continue;
      }

      $translated_body = $row['translations'][$langcode]['body'];
      $translated_body = $this->prepareMarkup($translated_body);
      $node->addTranslation($langcode, [
        'title' => $row['translations'][$langcode]['title'],
        'body' => [
          'value' => $translated_body,
          'format' => 'full_html',
        ],
      ]);
    }

    $node->save();
  }

  protected function getImage($markup) {
    $htmlDom = new \DOMDocument();
    @$htmlDom->loadHTML($markup);
    $xpath = new \DOMXPath($htmlDom);
    $images = $xpath->query('//img');

    $max = 0;
    $media_image = NULL;

    foreach ($images as $image) {
      /** @var \DOMElement $image */
      $src = $image->getAttribute('src');
      if (empty($src)) {
        continue;
      }

      if (!file_exists(DRUPAL_ROOT . $src)) {
        continue;
      }

      list($width, $height) = getimagesize(DRUPAL_ROOT . $src);
      if ($width * $height > $max) {
        $max = $width * $height;
        $media_image = $this->getMediaImage($src);
      }
    }

    return $media_image;
  }

  public function getMediaImage($src) {
    $src = str_replace('/sites/default/files', 'public:/', $src);
    $file = \Drupal::entityTypeManager()->getStorage('file')->loadByProperties([
      'uri' => $src,
    ]);

    $file = reset($file);

    $media_image = \Drupal::entityTypeManager()->getStorage('media')->loadByProperties([
      'field_media_image' => $file->id(),
    ]);
    $media_image = reset($media_image);
    return $media_image;
  }

  /**
   * Download a remote file to the local storage.
   *
   * @param string $source
   *   A file source path.
   * @param string $destination
   *   A file source destination.
   * @param bool $retry
   *   Whether to retry the downloaded with UTF8 encoded URL.
   *
   * @return bool
   *   TRUE if it's OK.
   *
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  protected function downloadRemoteFile(string $source, string $destination, bool $retry = TRUE) {
    if (strpos($source, '/sites/default/files') <= 7) {
      $source = 'https://cites.org' . $source;
    }

    $dirname = dirname($destination);
    $this->fileSystem->prepareDirectory($dirname, FileSystemInterface::CREATE_DIRECTORY | FileSystemInterface::MODIFY_PERMISSIONS);
    $destination = $this->fileSystem->realpath($dirname) . '/' . basename($destination);
    $destination = str_replace('%20', ' ', $destination);
    fopen($destination, 'w') or die('Problems');

    try {
      $this->client->request('GET', $source, ['sink' => $destination]);
      return TRUE;
    }
    catch (\Exception $e) {
      if (!$retry) {
        return FALSE;
      }
      $success = $this->downloadRemoteFile(utf8_decode($source), $destination, FALSE);
      if (!$success) {
        $this->logger->warning("The request for $source returned error {$e->getCode()}");
      }
      return $success;
    }
  }

  /**
   * Get the relative url from absolute url.
   *
   * @param string $url
   *   A relative or absolute url.
   *
   * @return string
   *   A relative url.
   */
  protected function getRelativeUrl(string $url) {
    return $url;
  }

  /**
   * Prepare markup for Drupal.
   *
   * This includes fixing absolute URLs, downloading remote images, replacing
   * [caption] tags with <figure> tags.
   *
   * @param string $markup
   *   A source markup.
   *
   * @return mixed
   *   A source markup.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  protected function prepareMarkup(string $markup) {
    $markup = $this->getProductionUrl($markup);

    // Replace [caption] tags with <figure>.
    preg_match_all('/(\[caption.*?\[\/caption\])/', $markup, $matches);
    if (!empty($matches[1])) {
      foreach ($matches[1] as $figure) {
        preg_match('/width="(.*?)"/', $figure, $width);
        $width = $width[1];

        preg_match('/src="(.*?)"/', $figure, $src);
        $src = $src[1];

        preg_match('/align="(.*?)"/', $figure, $align);
        $align = $align[1];

        preg_match('/\[caption.*?\](.*?)\[\/caption\]/', $figure, $text);
        $text = $text[1];
        $text = trim($text);
        $caption = strip_tags($text, '<p><span><b><strong><em><i><a>');

        $figure_html = $this->getHtmlFigure($src, $caption, $align, $width);
        $markup = str_replace($figure, $figure_html, $markup);
      }
    }
    $this->removeBackLinks($markup);
    $this->downloadFilesFromMarkup($markup);
    $this->fixInternalUrls($markup);
    $markup = $this->getRelativeUrl($markup);

    return $markup;
  }

  /**
   * Fix internal URLs.
   *
   * @param string $markup
   *   The markup.
   */
  protected function fixInternalUrls(string &$markup) {
    $markup = str_replace('https://cites.org', '', $markup);
    $markup = str_replace('http://cites.org', '', $markup);
    $markup = str_replace('https://www.cites.org', '', $markup);
    $markup = str_replace('http://www.cites.org', '', $markup);

    $htmlDom = new \DOMDocument();
    @$htmlDom->loadHTML($markup);
    $xpath = new \DOMXPath($htmlDom);
    $links = $xpath->query('//a');

    foreach ($links as $link) {
      /** @var \DOMElement $link */
      $src = $link->getAttribute('href');
      if (empty($src)) {
        continue;
      }

      if (strpos($src, 'sites/default/files') !== FALSE) {
        continue;
      }

      if (strpos($src, '/') !== 0) {
        continue;
      }

      if (isset($this->urlMapping[$src])) {
        if ($this->urlMapping[$src] == '') {
          $src = preg_quote($src, '/');
          $markup = preg_replace("/<a href=\"$src\">(.*?)<\/a>/m", '$1', $markup);
          continue;
        }

        $newSrc = $this->urlMapping[$src];
        $this->replaceLinks($markup, $src, $newSrc);
        continue;
      }

      if (in_array($src, $this->urlMapping)) {
        continue;
      }

      $newSrc = 'https://cites.org' . $src;
      $this->replaceLinks($markup, $src, $newSrc);
    }
  }

  protected function replaceLinks(&$markup, $old_link, $new_link) {
    $this->replacedLinks[$old_link] = $new_link;
    $markup = str_replace("\"$old_link\"", "\"$new_link\"", $markup);
    $markup = str_replace(">$old_link<", ">$new_link<", $markup);
  }

  /**
   * Get the production URL from test URL.
   *
   * @param string $url
   *   A production URL.
   *
   * @return string
   *   A processed production URL.
   */
  protected function getProductionUrl(string $url) {
    return $url;
  }

  /**
   * Download all the files in a markup and replace them with relative urls.
   *
   * @param string $markup
   *   A HTML source markup.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  protected function downloadFilesFromMarkup(string &$markup) {
    $extensions = [
      'png',
      'PNG',
      'jfif',
      'JFIF',
      'jpg',
      'jpeg',
      'JPG',
      'JPEG',
      'doc',
      'docx',
      'gif',
      'xls',
      'xlsx',
      'pdf',
    ];

    $htmlDom = new \DOMDocument();
    @$htmlDom->loadHTML($markup);
    $xpath = new \DOMXPath($htmlDom);
    $fileElements = $xpath->query('//a | //img');

    foreach ($fileElements as $fileElement) {
      /** @var \DOMElement $fileElement */
      $src = $fileElement->getAttribute('src');
      if (empty($src)) {
        $src = $fileElement->getAttribute('href');
      }
      if (strpos($src, 'ftp://') !== FALSE) {
        continue;
      }
      if (strpos($src, 'cites.org') === FALSE && strpos($src, '/sites/default/files') !== 0) {
        continue;
      }
      if (empty($src)) {
        continue;
      }
      $extension = pathinfo($src, PATHINFO_EXTENSION);

      if (!in_array($extension, $extensions)) {
        // $this->logger->warning("A link to $src was found.");
        continue;
      }

      $destination = 'public://imported-news/' . basename($src);
      $this->downloadRemoteFile($src, $destination);

      if (in_array($extension, ['pdf', 'doc', 'docx', 'xls', 'xlsx'])) {
        $title = trim($fileElement->textContent);
        $this->createMedia('file', $destination, $title);
      }
      else {
        $title = trim($fileElement->textContent);
        $this->createMedia('image', $destination, $title);
      }

      $newSrc = str_replace('public://', '/sites/default/files/', $destination);
      $this->replaceLinks($markup, $src, $newSrc);
    }
  }

  /**
   * Remove back links from the markup.
   *
   * @param string $markup
   *   A HTML source markup.
   */
  protected function removeBackLinks(string &$markup) {
    $markup = preg_replace('/<p><img.*?src="\/sites\/default\/files\/i\/arrow_orange\.gif".*?<\/p>/m', '', $markup);
  }

  /**
   * Create a media entity of type PDF of Image.
   *
   * @param string $type
   *   A media entity type.
   * @param string $uri
   *   A media uri.
   * @param string|null $title
   *   A media title.
   *
   * @return \Drupal\media\Entity\Media
   *   A Media entity.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   * @throws \Exception
   */
  protected function createMedia(string $type, string $uri, string $title = NULL) {
    if (!in_array($type, ['image', 'file'])) {
      throw new \Exception("Invalid type $type. Valid media types are image, video or pdf");
    }

    if (empty($title)) {
      $title = basename($uri);
    }

    if ($type == 'video') {
      $value = $uri;
    }
    else {
      $existing_file = $this->entityTypeManager->getStorage('file')
        ->loadByProperties(['uri' => $uri]);

      if (empty(!$existing_file)) {
        $file = reset($existing_file);
      }
      else {
        $file = File::create([
          'status' => 1,
          'uri' => $uri,
        ]);
        $file->save();
      }
      $value = $file->id();
    }

    $fields = [
      'image' => 'field_media_image',
      'file' => 'field_media_file',
    ];
    $field = $fields[$type];

    /** @var \Drupal\media\Entity\Media[] $existing_medias */
    $existing_medias = $this->entityTypeManager
      ->getStorage('media')
      ->loadByProperties([
        $field => $value,
        'bundle' => $type,
      ]);

    if (!empty($existing_medias)) {
      /** @var \Drupal\media\Entity\Media $existing_media */
      $existing_media = reset($existing_medias);
      return $existing_media;
    }

    /** @var \Drupal\media\Entity\Media $media */
    $media = Media::create([
      'bundle' => $type,
      $field => $value,
      'name' => $title,
    ]);

    $media->save();

    return $media;
  }

  /**
   * Create a <figure> tag with certain attributes.
   *
   * @param string $src
   *   An image SRC.
   * @param string $caption
   *   An image caption.
   * @param string $align
   *   An image align.
   * @param string $width
   *   An image width.
   *
   * @return string
   *   A HTML string.
   */
  protected function getHtmlFigure(string $src, string $caption, string $align, string $width) {
    return "<figure class=\"$align\" style=\"max-width: {$width}px;\"><img src=\"$src\" class=\"img-responsive\"><figcaption>$caption</figcaption></figure>";
  }

}
