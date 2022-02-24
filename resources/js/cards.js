const DEFAULT_PAPER_FORMAT = 'A4';
const DEFAULT_PAPER_ORIENTATION = 'v';
const DEFAULT_MARGIN_SIZE = 50;
const DEFAULT_PPI = 96;

const ALLOWED_PAPER_FORMAT_TYPES = ['A', 'B', 'C'];
const ALLOWED_PAPER_FORMAT_MIN_SIZE = 0;
const ALLOWED_PAPER_FORMAT_MAX_SIZE = 6;
const ALLOWED_PAPER_ORIENTATIONS = {
  'v': 'vertical',
  'h': 'horizontal'
};
const ALLOWED_MARGIN_MIN_SIZE = 0;
const ALLOWED_MARGIN_MAX_SIZE = 50;
const ALLOWED_PPI_MIN_VALUE = 60;
const ALLOWED_PPI_MAX_VALUE = 300;

const VERTICAL_PAPER_FORMATS = {
  "A0": {
    "width": 841,
    "height": 1189
  },
  "B0": {
    "width": 1000,
    "height": 1414
  },
  "C0": {
    "width": 917,
    "height": 1297
  }
}

const DEFAULT_FONT_FAMILY = 'Arial';
const DRAW_TEXT_MATCH_FONT_SIZE_COEFF = 0.8;
const DEFAULT_FONT_COLOR = 'white';

const FONT_STYLE_NORMAL = 'normal';

const TEXT_ALIGN_LEFT = 'left';
const TEXT_ALIGN_RIGHT = 'right';
const TEXT_ALIGN_CENTER = 'center';
const TEXT_ALIGN_JUSTIFY = 'justify';

const LINEAR_SCALE_SHORT_LINE_LENGTH = 0.5;

const CARD_WIDTH = 63.5;
const CARD_HEIGHT = 88;
const CARD_MARGIN = 3;

const CARD_ID_IDENTIFIER_TEXT_FONT_SIZE = 2;
const CARD_ID_IDENTIFIER_TEXT_Y_CHANGE = -0.5;
const CARD_ID_IDENTIFIER_TEXT_FONT_FAMILY = DEFAULT_FONT_FAMILY;

const CARD_DATA_FIELD_X = 'x';
const CARD_DATA_FIELD_Y = 'y';
const CARD_DATA_FIELD_IS_ACTIVE = 'isActive';
const CARD_DATA_FIELD_PATH = 'path';
const CARD_DATA_FIELD_FILE_DATA = 'fileData';
const CARD_DATA_FIELD_PARAMS = 'params';

const CARD_FORM_UNSELECTED_VALUE = '';
const CARD_FORM_UNSELECTED_NAME = '...';
const CARD_FORM_ID_PREFIX = 'card-form-';
const CARD_TYPES_ROOT_PATHS = {
  'god': '/files/data/records',
  'patrons': '/files/data/records'
};
const CARD_TYPE_SELECTED = 'patrons';

const CARD_BACKGROUND_COLOR = 'black';
const CARD_IMAGE_BACKGROUND_COLOR = 'black';

const FILE_DATA_IMAGES_KEY = 'images';
const FILE_DATA_NAMES_KEY = 'names';

let cardsData = [];
let filesContents = {};
let filesContentsErrors = {};

const getPaperFormatSelect = function() {
  return document.getElementById('paper-format');
}

const getPaperOrientationSelect = function() {
  return document.getElementById('paper-orientation');
}

const getMarginSizeSelect = function() {
  return document.getElementById('margin-size');
}

const getPpiSelect = function() {
  return document.getElementById('ppi');
}

const getCardsFormsDiv = function() {
  return document.getElementById('cards-forms');
}

const getPpiTestDiv = function() {
  return document.getElementById('ppi-test');
}

const getCanvas = function() {
  return document.getElementById('cards-canvas');
}

const getContext = function() {
  const canvas = getCanvas();

  return canvas.getContext('2d');
}

const buildForm = function() {
  buildPaperFormatSelect();
  buildPaperOrientationSelect();
  buildMarginSizeSelect();
  buildPpiSelect();

  buildCanvas();
}

const buildPaperFormatSelect = function() {
  const formats = getAllowedPaperFormats();
  const select = getPaperFormatSelect();

  for (const value of formats) {
    let option = document.createElement('option');

    option.value = value;
    option.innerHTML = value;
    if (value === DEFAULT_PAPER_FORMAT) {
      option.selected = 'selected';
    }
    select.appendChild(option);
  }
}

const buildPaperOrientationSelect = function() {
  const orientations = ALLOWED_PAPER_ORIENTATIONS;
  const select = getPaperOrientationSelect();

  for (const key in orientations) {
    let value = orientations[key];
    let option = document.createElement('option');

    option.value = key;
    option.innerHTML = value;
    if (key === DEFAULT_PAPER_ORIENTATION) {
      option.selected = 'selected';
    }
    select.appendChild(option);
  }
}

const getAllowedPaperFormats = function() {
  let result = [];

  for (const type of ALLOWED_PAPER_FORMAT_TYPES) {
    for (let size = ALLOWED_PAPER_FORMAT_MIN_SIZE; size <= ALLOWED_PAPER_FORMAT_MAX_SIZE; size++) {
      result.push(type + size);
    }
  }

  return result;
}

const buildMarginSizeSelect = function() {
  const select = getMarginSizeSelect();

  for (let value = ALLOWED_MARGIN_MIN_SIZE; value <= ALLOWED_MARGIN_MAX_SIZE; value++) {
    let option = document.createElement('option');

    option.value = value;
    option.innerHTML = value;
    if (value === DEFAULT_MARGIN_SIZE) {
      option.selected = 'selected';
    }
    select.appendChild(option);
  }
}

const buildPpiSelect = function() {
  const select = getPpiSelect();

  const detectedPpi = getDetectedPpi();
  if (!Number.isInteger(detectedPpi)) {
    detectedPpi = DEFAULT_PPI;
  }

  for (let value = ALLOWED_PPI_MIN_VALUE; value <= ALLOWED_PPI_MAX_VALUE; value++) {
    let option = document.createElement('option');

    option.value = value;
    option.innerHTML = value;
    if (value === detectedPpi) {
      option.selected = 'selected';
    }
    select.appendChild(option);
  }
}

const getDetectedPpi = function() {
  const div = getPpiTestDiv();

  if (div === null) {
    return DEFAULT_PPI;
  } else {
    return div.offsetWidth;
  }
}

const mm2px = function(milimeters) {
  const select = getPpiSelect();
  const ppi = select.value;
  const pixels = milimeters * ppi / 25.4;

  return pixels;
}

const getPrintableAreaSize = function() {
  const format = getPaperFormatSelect().value;
  const orientation = getPaperOrientationSelect().value;
  const margin = getMarginSizeSelect().value;
  const size = getFormatVerticalSize(format);

  let width = Math.max(0, size.width - margin);
  let height = Math.max(0, size.height - margin);

  if (orientation !== DEFAULT_PAPER_ORIENTATION) {
    let tmp = width;
    width = height;
    height = tmp;
  }

  return {
    'width': width,
    'height': height
  };
}

const getFormatVerticalSize = function(format) {
  let formatArray = format.split('');

  const formatType = formatArray.shift();
  const formatSize = formatArray.join('');

  let width = VERTICAL_PAPER_FORMATS[formatType + '0'].width;
  let height = VERTICAL_PAPER_FORMATS[formatType + '0'].height;

  for (let size = 1; size <= formatSize; size++) {
    let heightHalf = Math.floor(height / 2);
    height = width;
    width = heightHalf;
  }

  return {
    'width': width,
    'height': height
  };
}

const buildCanvas = async function() {
  const size = getPrintableAreaSize();
  const width = mm2px(size.width);
  const height = mm2px(size.height);

  scaleCanvas(width, height);
  drawPreparedCanvasArea(width, height);
  calculateCardsCoordinatesAndActivity(width, height);
  for (const cardId in cardsData) {
    drawCard(cardId);
  }
  await buildCardsForms();
}

const scaleCanvas = function(width, height) {
  const canvas = getCanvas();

  canvas.width = width;
  canvas.height = height;
}

const drawPreparedCanvasArea = function(width, height) {
  clearRectangle(0, 0, width, height);
  drawEmptyRectangle(0, 0, width, height, 'black');
  drawLinearScales(width, height);
}

const clearRectangle = function(x, y, width, height) {
  const context = getContext();

  context.clearRect(x, y, width, height);
}

const drawEmptyRectangle = function(x, y, width, height, color) {
  const context = getContext();

  context.lineWidth = 1;
  context.strokeStyle = color;
  context.strokeRect(x, y, width, height);
}

const drawFilledRectangle = function(x, y, width, height, color) {
  const context = getContext();

  context.fillStyle = color;
  context.fillRect(x, y, width, height);
}

const drawLinearScales = function(width, height) {
  const context = getContext();
  const milimeterPixels = mm2px(1);
  const shortLineLength = mm2px(LINEAR_SCALE_SHORT_LINE_LENGTH);
  const cardMargin = mm2px(CARD_MARGIN);

  context.lineWidth = 1;
  context.strokeStyle = "black";

  let lineNumber = 0;
  for (let x = cardMargin; x < width; x += milimeterPixels) {
    const lineLength = getLinearScaleLineLength(lineNumber, shortLineLength);
    lineNumber++;

    context.beginPath();
    context.moveTo(x, 0);
    context.lineTo(x, lineLength);
    context.stroke();
  }

  lineNumber = 0;
  for (let y = cardMargin; y < height; y += milimeterPixels) {
    const lineLength = getLinearScaleLineLength(lineNumber, shortLineLength);
    lineNumber++;

    context.beginPath();
    context.moveTo(0, y);
    context.lineTo(lineLength, y);
    context.stroke();
  }
}

const getLinearScaleLineLength = function (lineNumber, shortLineLength) {
  let multi = 1;

  if (lineNumber % 10 === 0) {
    multi = 4;
  } else if (lineNumber % 5 === 0) {
    multi = 2;
  }

  return multi * shortLineLength;
}

const printCanvas = function() {
  const canvas = getCanvas();

  const newWindow = window.open('', '', 'width=' + screen.availWidth + ',height=' + screen.availHeight);
  newWindow.document.open();
  newWindow.document.write('<img src="' + canvas.toDataURL() + '">');
  newWindow.document.addEventListener('load', function() {
      newWindow.focus();
      newWindow.print();
      newWindow.document.close();
      newWindow.close();
  }, true);
}

const calculateCardsCoordinatesAndActivity = function(areaWidth, areaHeight) {
  const cardWidth = mm2px(CARD_WIDTH);
  const cardHeight = mm2px(CARD_HEIGHT);
  const cardMargin = mm2px(CARD_MARGIN);

  const widthStep = cardWidth + cardMargin;
  const heightStep = cardHeight + cardMargin;

  let cardId = 0;
  for (let y = cardMargin; y + heightStep <= areaHeight; y += heightStep) {
    for (let x = cardMargin; x + widthStep <= areaWidth; x += widthStep) {
      cardId++;
      setCardIdCoordinatesAndActivity(cardId, x, y);
    }
  }

  const cardsDataLength = cardsData.length;
  for (cardId = cardId + 1; cardId < cardsDataLength; cardId++) {
    setCardIdInactivity(cardId);
  }
}

const getInitialCardData = function() {
  let result = {};

  result[CARD_DATA_FIELD_IS_ACTIVE] = false;
  result[CARD_DATA_FIELD_X] = 0;
  result[CARD_DATA_FIELD_Y] = 0;
  result[CARD_DATA_FIELD_PATH] = '';
  result[CARD_DATA_FIELD_FILE_DATA] = {};
  result[CARD_DATA_FIELD_PARAMS] = {};

  return result;
}

const setCardIdCoordinatesAndActivity = function(cardId, x, y) {
  if (cardsData[cardId] === undefined) {
    cardsData[cardId] = getInitialCardData();
  }

  cardsData[cardId][CARD_DATA_FIELD_IS_ACTIVE] = true;
  cardsData[cardId][CARD_DATA_FIELD_X] = x;
  cardsData[cardId][CARD_DATA_FIELD_Y] = y;
}

const setCardIdInactivity = function(cardId) {
  if (cardsData[cardId] === undefined) {
    cardsData[cardId] = getInitialCardData();
  }

  cardsData[cardId][CARD_DATA_FIELD_IS_ACTIVE] = false;
}

const drawCardIdIdentifierText = function(cardId, x, y) {
  context = getContext();

  context.font = mm2px(CARD_ID_IDENTIFIER_TEXT_FONT_SIZE) + 'px ' + CARD_ID_IDENTIFIER_TEXT_FONT_FAMILY;
  context.fillStyle = 'black';
  context.fillText(cardId + ':', x, y + mm2px(CARD_ID_IDENTIFIER_TEXT_Y_CHANGE));
}

const buildCardsForms = async function() {
  let cardsFormsDiv = getCardsFormsDiv();

  for (const cardId in cardsData) {
    const cardFormId = CARD_FORM_ID_PREFIX + cardId;
    const cardData = cardsData[cardId];
    const div = document.getElementById(cardFormId);

    if (cardData[CARD_DATA_FIELD_IS_ACTIVE]) {
      if (div === null) {
        const subDiv = document.createElement('div');
        subDiv.id = cardFormId;
        cardsFormsDiv.appendChild(subDiv);

        await rebuildCardForm(cardId);
      }
    } else if (div !== null) {
      cardsFormsDiv.removeChild(div);
    }
  }
}

const getFileContent = async function(path) {
  if (filesContents[path] !== undefined) {
    return filesContents[path];
  } else if (filesContentsErrors[path] !== undefined) {
    throw new Error(filesContentsErrors[path]);
  }

  let response = await fetch(path);
  if (!response.ok) {
    const errorMessage = 'HTTP status: ' + response.status;
    filesContentsErrors[path] = errorMessage;
    throw new Error(errorMessage);
  }

  const result = await response.text();
  filesContents[path] = result;

  return result;
}

const getJsonFromFile = async function(path) {
  const content = await getFileContent(path);

  return JSON.parse(content);
}

const getIndexData = async function(path) {
  let indexData = {};

  try {
    indexData = await getJsonFromFile(path + '/index.generated.json');
  } catch (error) {
    try {
      indexData = await getJsonFromFile(path + '/index.json');
    } catch (error) {
    }
  }

  return indexData;
}

const getHostname = function() {
  return window.location.hostname.toLowerCase();
}

const getProtocol = function() {
  return window.location.protocol;
}

const getLanguage = function() {
  const hostname = getHostname();

  return hostname.replace(/\..*$/, '');
}

const getTranslatedNameData = function(data, key) {
  const names = data[key];
  let language = getLanguage();
  let nameOtherLanguageSuffix = '';

  if (names[language] === undefined) {
    language = Object.keys(names)[0];
    nameOtherLanguageSuffix = ' [' + language + ']';
  }

  let name = names[language];
  if (name instanceof Array) {
    name = [...name].shift();
  }

  return [name + nameOtherLanguageSuffix, name, language];
}

const addSpanChildElement = function(element, text) {
  let span = document.createElement('span');
  span.innerHTML = text;
  element.appendChild(span);
}

const addCardFormSelectElement = function(element, id, options, selectedOption, onChangeFunction) {
  let select = document.createElement('select');
  select.id = id;
  select.onchange = onChangeFunction;

  options.forEach(function(value, key) {
    let option = document.createElement('option');
    option.value = key;
    option.innerHTML = value;
    if (key === selectedOption) {
      option.selected = 'selected';
    }

    select.appendChild(option);
  });

  element.appendChild(select);
}

const getCardTypeOptions = async function() {
  let result = new Map();

  const rootPaths = CARD_TYPES_ROOT_PATHS;
  for (const cardType in rootPaths) {
    const rootPath = rootPaths[cardType];
    const indexData = await getIndexData(rootPath);

    if (indexData[cardType] !== undefined) {
      result.set(cardType, getTranslatedNameData(indexData, cardType)[0]);
    }
  }

  return result;
}

const getCardTypeRootPath = function(cardType) {
  return CARD_TYPES_ROOT_PATHS[cardType];
}

const getJsonFileData = async function(filePath) {
  let result = {};

  try {
    result = await getJsonFromFile(filePath);
  } catch (error) {
  }

  return result;
}

const prepareSelectOptions = function(options) {
  let unselectedValue;
  for (const key of options.keys()) {
    const intKey = parseInt(key);
    if (key === CARD_FORM_UNSELECTED_VALUE) {
      unselectedValue = options.get(key);
    } else if (intKey === 'NaN' || key.toString().length !== intKey.toString().length) {
      return options;
    }
  }

  if (unselectedValue !== undefined) {
    options.delete(CARD_FORM_UNSELECTED_VALUE);
  }

  const orderedOptions = new Map([...options.entries()].sort((a, b) => a[1] > b[1]));
  const result = new Map();

  if (unselectedValue !== undefined) {
    result.set(CARD_FORM_UNSELECTED_VALUE, unselectedValue);
  }
  orderedOptions.forEach(function(value, key) {
    result.set(key.toString(), value);
  });

  return result;
}

const buildCardFormSelects = async function(cardId, path, contextPath, options) {
  const cardFormId = CARD_FORM_ID_PREFIX + cardId;
  const parentElement = document.getElementById(cardFormId);
  const stepNumber = contextPath.length + 1;
  const selectedOption = path[stepNumber - 1];
  const cardFormSelectId = cardFormId + '-' + stepNumber;

  if (options !== {}) {
    const onChangeFunction = function() {
      saveCardDataPath(cardId, stepNumber);
    }
    const preparedOptions = prepareSelectOptions(options);
    addCardFormSelectElement(parentElement, cardFormSelectId, preparedOptions, selectedOption, onChangeFunction);
  }

  if (selectedOption === undefined || options.get(selectedOption) === undefined) {
    return;
  }

  contextPath.push(selectedOption);
  const rootPath = getCardTypeRootPath(path[0]);
  const fullContextPathString = rootPath + '/' + contextPath.join('/');

  if (selectedOption !== CARD_FORM_UNSELECTED_VALUE) {
    const fileData = await getJsonFileData(fullContextPathString + '.json');
    if (Object.keys(fileData).length > 0) {
      cardsData[cardId][CARD_DATA_FIELD_FILE_DATA] = fileData;
      drawCard(cardId);

      return;
    }
  }

  const indexData = await getIndexData(fullContextPathString);
  if (Object.keys(indexData).length === 0) {
    return;
  }

  const nextOptions = new Map();
  nextOptions.set(CARD_FORM_UNSELECTED_VALUE, CARD_FORM_UNSELECTED_NAME);
  for (const option in indexData) {
    nextOptions.set(option, getTranslatedNameData(indexData, option)[0]);
  }

  await buildCardFormSelects(cardId, path, contextPath, nextOptions);
}

const rebuildCardForm = async function(cardId) {
  const cardFormId = CARD_FORM_ID_PREFIX + cardId;
  const cardData = cardsData[cardId];

  //remove existing elements
  const div = document.getElementById(cardFormId);
  while (div.lastChild) {
    div.removeChild(div.lastChild);
  }

  //label
  addSpanChildElement(div, cardId + ': ');

  //card path selects
  let path = cardData[CARD_DATA_FIELD_PATH];
  let pathArr = path.split('/').filter(o => o);
  const options = await getCardTypeOptions();
  if (pathArr.length === 0) {
    pathArr.push(CARD_TYPE_SELECTED);
  }
  await buildCardFormSelects(cardId, pathArr, [], options);

  //card input fields
  //...todo
}

const saveCardDataPath = async function(cardId, selectId) {
  const cardFormId = CARD_FORM_ID_PREFIX + cardId;

  let pathArr = [];
  let formId = 0;
  while (true) {
    formId++;

    if (selectId < formId) {
      break;
    }

    const cardFormSelectId = cardFormId + '-' + formId;
    const element = document.getElementById(cardFormSelectId);
    if (element === null) {
      break;
    }

    const selectedValue = element.value;
    if (selectedValue === CARD_FORM_UNSELECTED_VALUE) {
      break;
    }

    pathArr.push(selectedValue);
  }
  cardsData[cardId][CARD_DATA_FIELD_PATH] = pathArr.join('/');

  await rebuildCardForm(cardId);
}

const drawCardImage = function(imageData, x, y, width, height) {
  const imageUrl = imageData['file-url'];
  const imageAreaTopLeftX = imageData['area-top-left-x'];
  const imageAreaTopLeftY = imageData['area-top-left-y'];
  const imageAreaBottomRightX = imageData['area-bottom-right-x'];
  const imageAreaBottomRightY = imageData['area-bottom-right-y'];

  const imageAreaWidth = imageAreaBottomRightX - imageAreaTopLeftX;
  const imageAreaHeight = imageAreaBottomRightY - imageAreaTopLeftY;

  const widthCoeff = width / imageAreaWidth;
  const heightCoeff = height / imageAreaHeight;
  let coeff = widthCoeff;
  if (Math.abs(1 - widthCoeff) > Math.abs(1 - heightCoeff)) {
    coeff = heightCoeff;
  }

  //fix problem with scale in greater PPI's
  if (widthCoeff + heightCoeff >= 2) {
    if (coeff === heightCoeff) {
      coeff = widthCoeff;
    } else {
      coeff = heightCoeff;
    }
  }

  const imageWidth = imageAreaWidth * coeff / heightCoeff;
  const imageHeight = imageAreaHeight * coeff / widthCoeff;
  const imageMoveX = imageWidth / 2 - imageAreaWidth / 2;
  const imageMoveY = imageHeight / 2 - imageAreaHeight / 2;

  drawFilledRectangle(x, y, width, height, CARD_IMAGE_BACKGROUND_COLOR);

  let image = new Image();
  image.onload = function() {
    context.drawImage(
      image,
      imageAreaTopLeftX - imageMoveX, imageAreaTopLeftY - imageMoveY, imageWidth, imageHeight,
      x, y, width, height
    );
  }

  image.src = imageUrl;
}

const drawQrCode = function(path, x, y, size, darkColor, lightColor) {
  const context = getContext();
  const divElement = document.createElement('div');

  const text = getProtocol() + '//' + getHostname() + '/' + path;

  const options = {
    width: size,
    height: size,
    colorDark : darkColor,
    colorLight : lightColor,
    correctLevel : QRCode.CorrectLevel.H
  };
  let qrCode = new QRCode(divElement, options);
  qrCode.makeCode(text);

  let image = divElement.querySelector('img');
  image.onload = function() {
    context.drawImage(image, x, y);
  }
}

const drawText = function(text, x, y, width, height, fontColor, fontStyle = FONT_STYLE_NORMAL, align = TEXT_ALIGN_CENTER) {
  const context = getContext();

  const fontSize = DRAW_TEXT_MATCH_FONT_SIZE_COEFF * height;
  context.font = fontStyle + ' ' + fontSize + 'px ' + DEFAULT_FONT_FAMILY;
  context.textAlign = 'left';
  context.textBaseline = 'alphabetic';
  context.fillStyle = fontColor;

  const textWidth = context.measureText(text).width;

  if (textWidth > width || align === TEXT_ALIGN_JUSTIFY) {
    const scale = width / textWidth;

    context.save();
    context.translate(x, y + fontSize);
    context.scale(scale, 1);
    context.fillText(text, 0, 0);
    context.restore();
  } else {
    let moveX = 0;
    if (align === TEXT_ALIGN_RIGHT) {
      moveX = width - textWidth;
    } else if (align === TEXT_ALIGN_CENTER) {
      moveX = (width - textWidth) / 2;
    }
    context.fillText(text, x + moveX, y + fontSize);
  }
}

const drawCard = function(cardId) {
  const cardData = cardsData[cardId];
  if (!cardData[CARD_DATA_FIELD_IS_ACTIVE]) {
    return;
  }

  const cardWidth = mm2px(CARD_WIDTH);
  const cardHeight = mm2px(CARD_HEIGHT);

  const x = cardData[CARD_DATA_FIELD_X];
  const y = cardData[CARD_DATA_FIELD_Y];
  const data = cardData[CARD_DATA_FIELD_FILE_DATA];

  drawCardIdIdentifierText(cardId, x, y);
  clearRectangle(x, y, cardWidth, cardHeight);
  drawEmptyRectangle(x, y, cardWidth, cardHeight, 'black');

  if (Object.keys(data).length > 0) {
    const fontColor = DEFAULT_FONT_COLOR;
    const fontStyle = FONT_STYLE_NORMAL;
    const textAlign = TEXT_ALIGN_CENTER;

    const nameData = getTranslatedNameData(data, FILE_DATA_NAMES_KEY);

    const marginSize = mm2px(3);

    const nameWidth = cardWidth - 2 * marginSize;
    const nameHeight = mm2px(7);
    const nameX = x + marginSize;
    const nameY = y;
    const nameColor = 'white';

    const languageWidth = mm2px(3);
    const languageHeight = mm2px(3);
    const languageX = x + cardWidth - languageWidth - marginSize;
    const languageY = y + nameHeight;
    const languageColor = 'red';

    const imageSize = mm2px(36);
    const imageWidth = imageSize;
    const imageHeight = imageSize;
    const imageX = x + marginSize;
    const imageY = y + nameHeight;

    const qrCodeSize = mm2px(20);
    const qrCodeX = x + cardWidth - qrCodeSize - marginSize;
    const qrCodeY = y + nameHeight + imageHeight - qrCodeSize;
    const qrCodeDarkColor = 'yellow';
    const qrCodeLightColor = 'black';

    //background
    drawFilledRectangle(x, y, cardWidth, cardHeight, CARD_BACKGROUND_COLOR);

    //image
    drawCardImage(data[FILE_DATA_IMAGES_KEY]['1'], imageX, imageY, imageWidth, imageHeight);

    //QR code
    drawQrCode(cardData[CARD_DATA_FIELD_PATH], qrCodeX, qrCodeY, qrCodeSize, qrCodeDarkColor, qrCodeLightColor);

    //name
    drawText(nameData[1], nameX, nameY, nameWidth, nameHeight, nameColor, fontStyle, textAlign);

    //language
    drawText(nameData[2].toUpperCase(), languageX, languageY, languageWidth, languageHeight, languageColor, fontStyle, TEXT_ALIGN_JUSTIFY);

    //death
    //...

    //attributes
    //...

    //religious orders
    //...

    //card owner
    //...

    //patrons strength - only patrons
    //...

    //patron color status
    //...
  }
}

buildForm();
