package com.jimplush.goose.images;

import com.jimplush.goose.Configuration;
import com.jimplush.goose.network.HtmlFetcher;
import com.jimplush.goose.texthelpers.HashUtils;
import org.apache.http.Header;
import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpHead;
import org.apache.http.client.protocol.ClientContext;
import org.apache.http.protocol.BasicHttpContext;
import org.apache.http.protocol.HttpContext;
import org.apache.log4j.Logger;
import org.jsoup.nodes.Document;
import org.jsoup.nodes.Element;
import org.jsoup.select.Elements;

import java.io.File;
import java.io.IOException;
import java.net.MalformedURLException;
import java.net.URL;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

/**
 * User: jim plush
 * Date: 12/19/10
 */

/**
 * This image extractor will attempt to find the best image nearest the article.
 * Unfortunately this is a slow process since we're actually downloading the image itself
 * to inspect it's actual height/width and area metrics since most of the time these aren't
 * in the image tags themselves or can be falsified.
 * We'll weight the images in descending order depending on how high up they are compared to the top node content
 */
public class BestImageGuesser implements ImageExtractor {
  private static final Logger logger = Logger.getLogger(BestImageGuesser.class);

  /**
   * holds an httpclient connection object for doing head requests to get image sizes
   */
  HttpClient httpClient;

  /**
   * holds the document that we're extracting the image from
   */
  Document doc;


  /**
   * this lists all the known bad button names that we have
   */
  String regExBadImageNames;


  /**
   * holds the result of our image extraction
   */
  Image image;


  /**
   * the webpage url that we're extracting content from
   */
  String targetUrl;


  /**
   * stores a hash of our url for reference and image processing
   */
  String linkhash;

  /**
   * What's the minimum bytes for an image we'd accept is
   */
  int minBytesForImages;


  /**
   * location to store temporary image files if need be
   */
  String tempStoragePath;


  /**
   * holds the global configuration object
   */
  Configuration config;


  public BestImageGuesser(Configuration config, HttpClient httpClient, String targetUrl) {
    this.httpClient = httpClient;

    StringBuilder sb = new StringBuilder();
    // create negative elements
    sb.append(".html|.gif|.ico|button|twitter.jpg|facebook.jpg|digg.jpg|digg.png|delicious.png|facebook.png|reddit.jpg|doubleclick|diggthis|diggThis|adserver|/ads/|ec.atdmt.com");
    sb.append("|mediaplex.com|adsatt|view.atdmt");
    this.regExBadImageNames = sb.toString();

    image = new Image();

    this.config = config;

    this.targetUrl = targetUrl;
    this.linkhash = HashUtils.md5(this.targetUrl);

  }

  public Image getBestImage(Document doc, Element topNode) {
    this.doc = doc;
    logger.info("Starting to Look for the Most Relavent Image");

    if (image.getImageSrc() == null) {
      this.checkForKnownElements();
    }

    // I'm checking for large images first because a lot of the meta tags contained thumbnail size images instead of the goods!
    // so we want to try and get the biggest image around the content area as possible.
    if (image.getImageSrc() == null) {
      this.checkForLargeImages(topNode, 0, 0);
    }

    // fall back to meta tags, these can sometimes be inconsistent which is why we favor them less
    if (image.getImageSrc() == null) {
      this.checkForMetaTag();
    }


    return image;
  }

  private boolean checkForMetaTag() {

    if (this.checkForLinkTag()) {
      return true;
    }

    if (this.checkForOpenGraphTag()) {
      return true;
    }

    logger.info("unable to find meta image");
    return false;


  }

  /**
   * checks to see if we were able to find open graph tags on this page
   *
   * @return
   */
  private boolean checkForOpenGraphTag() {
    try {
      Elements meta = this.doc.select("meta[property~=og:image]");
      for (Element item : meta) {
        if (item.attr("content").length() < 1) {
          return false;
        }
        this.image.setImageSrc(this.buildImagePath(item.attr("content")));
        this.image.setImageExtractionType("opengraph");
        this.image.setConfidenceScore(100);
        this.image.setBytes(this.getBytesForImage(this.buildImagePath(item.attr("content"))));
        logger.info("open graph tag found, using it");
        return true;
      }
      return false;
    } catch (Exception e) {
      e.printStackTrace();
      return false;
    }
  }

  /**
   * checks to see if we were able to find open graph tags on this page
   *
   * @return
   */
  private boolean checkForLinkTag() {
    try {
      Elements meta = this.doc.select("link[rel~=image_src]");
      for (Element item : meta) {
        if (item.attr("href").length() < 1) {
          return false;
        }
        this.image.setImageSrc(this.buildImagePath(item.attr("href")));
        this.image.setImageExtractionType("linktag");
        this.image.setConfidenceScore(100);
        this.image.setBytes(this.getBytesForImage(this.buildImagePath(item.attr("href"))));
        logger.info("link tag found, using it");
        return true;
      }
      return false;
    } catch (Exception e) {
      logger.error(e);
      return false;
    }
  }

  public ArrayList<Element> getAllImages() {
    return null;  //To change body of implemented methods use File | Settings | File Templates.
  }


  /**
   * although slow the best way to determine the best image is to download them and check the actual dimensions of the image when on disk
   * so we'll go through a phased approach...
   * 1. get a list of ALL images from the parent node
   * 2. filter out any bad image names that we know of (gifs, ads, etc..)
   * 3. do a head request on each file to make sure it meets our bare requirements
   * 4. any images left over let's do a full GET request, download em to disk and check their dimensions
   * 5. Score images based on different factors like height/width and possibly things like color density
   *
   * @param node
   */
  private void checkForLargeImages(Element node, int parentDepth, int siblingDepth) {
    Elements images = null;
    try {
      images = node.select("img");
    } catch (NullPointerException e) {
      return;
    }


    String nodeId = this.getNodeIds(node);
    logger.info("checkForLargeImages: Checking for large images, found: " + images.size() + " - parent depth: " + parentDepth + " sibling depth: " + siblingDepth + " for node: " + nodeId);
    ArrayList<Element> goodImages;

    goodImages = this.filterBadNames(images);
    logger.info("checkForLargeImages: After filterBadNames we have: " + goodImages.size());

    goodImages = findImagesThatPassByteSizeTest(goodImages);
    logger.info("checkForLargeImages: After findImagesThatPassByteSizeTest we have: " + goodImages.size());

    HashMap<Element, Float> imageResults = downloadImagesAndGetResults(goodImages, parentDepth);


    // pick out image with high score
    Element highScoreImage = null;
    for (Element image : imageResults.keySet()) {
      if (highScoreImage == null) {
        highScoreImage = image;
      } else {

        if (imageResults.get(image) > imageResults.get(highScoreImage)) {
          highScoreImage = image;
        }
      }

    }
    if (highScoreImage != null) {


      File f = new File(highScoreImage.attr("tempImagePath"));
      this.image.setTopImageNode(highScoreImage);
      this.image.setImageSrc(this.buildImagePath(highScoreImage.attr("src")));
      this.image.setImageExtractionType("bigimage");
      this.image.setBytes((int) f.length());
      if (imageResults.size() > 0) {
        this.image.setConfidenceScore(100 / imageResults.size());
      } else {
        this.image.setConfidenceScore(0);
      }
      logger.info("High Score Image is: " + this.buildImagePath(highScoreImage.attr("src")));
    } else {
      logger.info("unable to find a large image, going to fall back modez. depth: " + parentDepth);
      if (parentDepth < 2) {

        // we start at the top node then recursively go up to siblings/parent/grandparent to find something good

        Element prevSibling = node.previousElementSibling();
        if (prevSibling != null) {
          logger.info("About to do a check against the sibling element, tagname: '" + prevSibling.tagName() + "' class: '" + prevSibling.attr("class") + "' id: '" + prevSibling.id() + "'");
          siblingDepth++;
          this.checkForLargeImages(prevSibling, parentDepth, siblingDepth);
        } else {
          logger.info("no more sibling nodes found, time to roll up to parent node");
          parentDepth++;
          this.checkForLargeImages(node.parent(), parentDepth, siblingDepth);
        }

      }
    }

  }


  /**
   * loop through all the images and find the ones that have the best bytez to even make them a candidate
   *
   * @param images
   * @return
   */
  private ArrayList<Element> findImagesThatPassByteSizeTest(ArrayList<Element> images) {
    int cnt = 0;
    ArrayList<Element> goodImages = new ArrayList<Element>();
    for (Element image : images) {
      if (cnt > 30) {
        logger.info("Abort! they have over 30 images near the top node: " + this.doc.baseUri());
        return goodImages;
      }
      int bytes = this.getBytesForImage(image.attr("src"));
      // we dont want anything over 15 megs
      if ((bytes == 0 || bytes > this.minBytesForImages) && bytes < 15728640) {
        logger.info("findImagesThatPassByteSizeTest: Found potential image - size: " + bytes + " src: " + image.attr("src"));
        goodImages.add(image);
      } else {
        image.remove();
      }
      cnt++;
    }
    return goodImages;
  }


  /**
   * takes a list of image elements and filters out the ones with bad names
   *
   * @param images
   * @return
   */
  private ArrayList<Element> filterBadNames(Elements images) {
    ArrayList<Element> goodImages = new ArrayList<Element>();
    for (Element image : images) {
      if (this.isOkImageFileName(image)) {
        goodImages.add(image);
      } else {
        image.remove();
      }
    }
    return goodImages;
  }

  /**
   * will check the image src against a list of bad image files we know of like buttons, etc...
   *
   * @return
   */
  private boolean isOkImageFileName(Element imageNode) {
    if (imageNode.attr("src").length() == 0) {
      return false;
    }
    Pattern semiBad = Pattern.compile(this.regExBadImageNames);
    Matcher matcher = semiBad.matcher(imageNode.attr("src"));
    if (matcher.find()) {
      logger.info("Found bad filename for image: " + imageNode.attr("src"));
      return false;
    }
    return true;
  }

  /**
   * returns a string with debug info about this node
   *
   * @param node
   * @return
   */
  private String getNodeIds(Element node) {
    String stuff = "";
    stuff = "tag: " + node.tagName() + " class: " + node.className() + " ID: " + node.id();
    return stuff;
  }


  /**
   * in here we check for known image contains from sites we've checked out like yahoo, techcrunch, etc... that have
   * known  places to look for good images.
   * //todo enable this to use a series of settings files so people can define what the image ids/classes are on specific sites
   */
  private void checkForKnownElements() {
    Element knownImage = null;

    logger.info("Checking for known images from large sites");
    String[] knownIds = {"yn-story-related-media", "cnn_strylccimg300cntr", "big_photo"};

    for (String knownName : knownIds) {
      try {
        Element known = this.doc.getElementById(knownName);

        if (known == null) {
          known = this.doc.getElementsByClass(knownName).first();
        }

        if (known != null) {
          Element mainImage = known.getElementsByTag("img").first();
          if (mainImage != null) {
            knownImage = mainImage;
            logger.info("Got Image: " + mainImage.attr("src"));
          }
        }

      } catch (NullPointerException e) {
        logger.info(e.toString(), e);
      }

    }

    if (knownImage != null) {
      this.image.setImageSrc(this.buildImagePath(knownImage.attr("src")));
      this.image.setImageExtractionType("known");
      this.image.setConfidenceScore(90);
      this.image.setBytes(this.getBytesForImage(knownImage.attr("src")));
    } else {
      logger.info("No known images found");
    }


  }

  /**
   * This method will take an image path and build out the absolute path to that image
   * using the initial url we crawled so we can find a link to the image if they use relative urls like ../myimage.jpg
   *
   * @param image
   * @return
   */
  private String buildImagePath(String image) {
    URL pageURL;
    String newImage = image.replace(" ", "%20");
    try {
      pageURL = new URL(this.targetUrl);
      URL imageURL = new URL(pageURL, image);

      newImage = imageURL.toString();
    } catch (MalformedURLException e) {
      logger.error("Unable to get Image Path: " + image);
    }
    return newImage;

  }


  /**
   * does the HTTP HEAD request to get the image bytes for this images
   *
   * @param src
   * @return
   */
  private int getBytesForImage(String src) {
    int bytes = 0;
    HttpHead httpget = null;
    try {
      String link = this.buildImagePath(src);
      link = link.replace(" ", "%20");

      HttpContext localContext = new BasicHttpContext();
      localContext.setAttribute(ClientContext.COOKIE_STORE, HtmlFetcher.emptyCookieStore);

      httpget = new HttpHead(link);

      HttpResponse response;

      response = httpClient.execute(httpget, localContext);

      HttpEntity entity = response.getEntity();

      bytes = this.minBytesForImages + 1;

      try {
        int currentBytes = (int) entity.getContentLength();
        Header contentType = entity.getContentType();
        if (contentType.getValue().contains("image")) {
          bytes = currentBytes;
        }
      } catch (NullPointerException e) {

      }

    } catch (Exception e) {

      logger.error(e.toString());
    } finally {
      try {
        httpget.abort();
      } catch (NullPointerException e) {
        logger.info("HttpGet is null, can't abortz");
      }
    }

    return bytes;
  }

  /**
   * download the images to temp disk and set their dimensions
   * <p/>
   * we're going to score the images in the order in which they appear so images higher up will have more importance,
   * we'll count the area of the 1st image as a score of 1 and then calculate how much larger or small each image after it is
   * we'll also make sure to try and weed out banner type ad blocks that have big widths and small heights or vice versa
   * so if the image is 3rd found in the dom it's sequence score would be 1 / 3 = .33 * diff in area from the first image
   *
   * @param images
   * @return
   */
  private HashMap<Element, Float> downloadImagesAndGetResults(ArrayList<Element> images, int depthLevel) {
    HashMap<Element, Float> imageResults = new HashMap<Element, Float>();

    int cnt = 1;
    int initialArea = 0;

    for (Element image : images) {

      if (cnt > 30) {
        logger.info("over 30 images attempted, that's enough for now");
        break;
      }

      // download image to local disk
      try {
        String imageSource = this.buildImagePath(image.attr("src"));


        String localSrcPath = ImageSaver.storeTempImage(this.httpClient, this.linkhash, imageSource, this.config);
        if (localSrcPath == null) {
          logger.info("unable to store this image locally: IMGSRC: " + image.attr("src") + " BUILD SRC: " + imageSource);
          continue;
        }
        logger.info("Starting image: " + localSrcPath);

        // set the temporary image path as an attribute on this node
        image.attr("tempImagePath", localSrcPath);

        int width;
        int height;
        try {
          ImageDetails imageDims = ImageUtils.getImageDimensions(config.getImagemagickIdentifyPath(), localSrcPath);
          width = imageDims.getWidth();
          height = imageDims.getHeight();

          // check for minimim depth requirements, if we're branching out wider in the dom, only get big images
          if (depthLevel > 1) {
            if (width < 300) {
              logger.info("going depthlevel: " + depthLevel + " and img was only: " + width + " wide: " + localSrcPath);
              continue;
            }

          }

        } catch (IOException e) {
          throw e;
        }


        // Check dimensions to make sure it doesn't seem like a banner type ad
        if (this.isBannerDimensions(width, height)) {
          logger.info(image.attr("src") + " seems like a fishy image dimension wise, skipping it");
          image.remove();
          continue;
        }

        if (width < 50) {
          logger.info(image.attr("src") + " is too small width: " + width + " removing..");
          image.remove();
          continue;
        }

        float sequenceScore = (float) 1 / cnt;
        int area = width * height;

        float totalScore = 0;
        if (initialArea == 0) {
          initialArea = area;
          totalScore = 1;
        } else {
          // let's see how many times larger this image is than the inital image
          float areaDifference = (float) area / initialArea;
          totalScore = (float) sequenceScore * areaDifference;
        }
        logger.info(imageSource + " Area is: " + area + " sequence score: " + sequenceScore + " totalScore: " + totalScore);

        cnt++;
        imageResults.put(image, totalScore);

      } catch (SecretGifException e) {

        continue;

      } catch (Exception e) {
        logger.error(e.toString());
        continue;
      }


    }


    return imageResults;
  }


  /**
   * returns true if we think this is kind of a bannery dimension
   * like 600 / 100 = 6 may be a fishy dimension for a good image
   *
   * @param width
   * @param height
   */
  private boolean isBannerDimensions(Integer width, Integer height) {
    if (width == height) {
      return false;
    }

    if (width > height) {
      float diff = (float) width / height;
      if (diff > 5) {
        return true;
      }
    }

    if (height > width) {
      float diff = (float) height / width;
      if (diff > 5) {
        return true;
      }
    }
    return false;
  }


  public int getMinBytesForImages() {
    return minBytesForImages;
  }

  public void setMinBytesForImages(int minBytesForImages) {
    this.minBytesForImages = minBytesForImages;
  }

  public String getTempStoragePath() {
    return tempStoragePath;
  }

  public void setTempStoragePath(String tempStoragePath) {
    this.tempStoragePath = tempStoragePath;
  }
}
