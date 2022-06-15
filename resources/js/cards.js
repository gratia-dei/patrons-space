const DEFAULT_PAPER_FORMAT = 'A4';
const DEFAULT_PAPER_ORIENTATION = 'v';
const DEFAULT_MARGIN_SIZE = 50;
const DEFAULT_PPI = 96;

const CANVAS_SCALE_FACTOR = 10;

const UNKNOWN = '?';

const ALLOWED_PAPER_FORMAT_TYPES = ['A', 'B', 'C'];
const ALLOWED_PAPER_FORMAT_MIN_SIZE = 0;
const ALLOWED_PAPER_FORMAT_MAX_SIZE = 6;
const ALLOWED_PAPER_ORIENTATIONS = {
  'v': 'lang-vertical',
  'h': 'lang-horizontal'
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
const CARD_DATA_FIELD_REFRESHED_PARAMS_PATH = 'refreshedParamsPath';
const CARD_DATA_FIELD_FILE_DATA = 'fileData';
const CARD_DATA_FIELD_PARAMS = 'params';

const CARD_FORM_UNSELECTED_VALUE = '';
const CARD_FORM_UNSELECTED_NAME = '...';
const CARD_FORM_ID_PREFIX = 'card-form-';
const CARD_FORM_INPUTS_DIV_ID_PREFIX = 'card-form-inputs-';

const CARD_TYPE_GOD = 'god';
const CARD_TYPE_PATRONS = 'patrons';

const CARD_TYPES_ROOT_PATHS = {
  [CARD_TYPE_GOD]: '/files/data/records',
  [CARD_TYPE_PATRONS]: '/files/data/records'
};
const CARD_TYPE_SELECTED = CARD_TYPE_PATRONS;

const CARD_BACKGROUND_COLOR = 'black';
const CARD_IMAGE_BACKGROUND_COLOR = 'black';

const FILE_DATA_IMAGES_KEY = 'images';
const FILE_DATA_NAMES_KEY = 'names';
const FILE_DATA_DEATH_KEY = 'died';
const FILE_DATA_CATEGORIES_KEY = 'categories';
const FILE_DATA_ORDER_KEY = 'order';

const CARD_DATA_PARAMS_FIELD_NAME = 'lang-name';
const CARD_DATA_PARAMS_FIELD_LANGUAGE = 'lang-language';
const CARD_DATA_PARAMS_FIELD_IMAGE_FILE_URL = 'lang-image-file-url';
const CARD_DATA_PARAMS_FIELD_IMAGE_AREA_TOP_LEFT_X = 'lang-image-area-top-left-x';
const CARD_DATA_PARAMS_FIELD_IMAGE_AREA_TOP_LEFT_Y = 'lang-image-area-top-left-y';
const CARD_DATA_PARAMS_FIELD_IMAGE_AREA_BOTTOM_RIGHT_X = 'lang-image-area-bottom-right-x';
const CARD_DATA_PARAMS_FIELD_IMAGE_AREA_BOTTOM_RIGHT_Y = 'lang-image-area-bottom-right-y';
const CARD_DATA_PARAMS_FIELD_DEATH = 'lang-date-of-death';
const CARD_DATA_PARAMS_FIELD_CATEGORIES = 'lang-categories';
const CARD_DATA_PARAMS_FIELD_ORDER = 'lang-order';
const CARD_DATA_PARAMS_FIELD_CARD_OWNER = 'lang-card-owner';
const CARD_DATA_PARAMS_FIELD_QR_CODE_URL = 'lang-qr-code-url';

const TRINITY_SYMBOL_URL = 'https://upload.wikimedia.org/wikipedia/commons/d/d6/Scutum_fidei_LAT.svg';

const STATUS_COLOR_BORDER = '#000000';
const STATUS_COLOR_CIRCLE = '#DDDDDD';
const STATUS_COLOR_RED = '#F43545';
const STATUS_COLOR_ORANGE = '#FA8901';
const STATUS_COLOR_YELLOW = '#FAD717';
const STATUS_COLOR_GREEN = '#00BA71';
const STATUS_COLOR_BLUE = '#00C2DE';
const STATUS_COLOR_INDIGO = '#00418D';
const STATUS_COLOR_VIOLET = '#5F2879';
const STATUS_COLOR_WHITE = '#FFFFFF';

const PATRON_RANK_BORDER_COLOR = '#BBBBBB';
const PATRON_RANK_CELL_SIZE = 2;
const PATRON_RANK_ROWS = 10;
const PATRON_RANK_COLUMNS = 10;

const IMAGE_DATA_FIELD_FILE_URL = 'file-url';
const IMAGE_DATA_FIELD_AREA_TOP_LEFT_X = 'area-top-left-x';
const IMAGE_DATA_FIELD_AREA_TOP_LEFT_Y = 'area-top-left-y';
const IMAGE_DATA_FIELD_AREA_BOTTOM_RIGHT_X = 'area-bottom-right-x';
const IMAGE_DATA_FIELD_AREA_BOTTOM_RIGHT_Y = 'area-bottom-right-y';
const CARD_TYPE_BACKGROUND_IMAGES = {
  [CARD_TYPE_PATRONS]: {
    [IMAGE_DATA_FIELD_FILE_URL]: '-add-background-file-url-here-',
    [IMAGE_DATA_FIELD_AREA_TOP_LEFT_X]: 0,
    [IMAGE_DATA_FIELD_AREA_TOP_LEFT_Y]: 0,
    [IMAGE_DATA_FIELD_AREA_BOTTOM_RIGHT_X]: 100,
    [IMAGE_DATA_FIELD_AREA_BOTTOM_RIGHT_Y]: 100,
  }
};
CARD_TYPE_BACKGROUND_IMAGES[CARD_TYPE_GOD] = CARD_TYPE_BACKGROUND_IMAGES[CARD_TYPE_PATRONS];

CSS_INVISIBLE = 'display: none';

PATRON_CATEGORIES_JSON_FILE = '/files/data/records/categories.json';
FEMALE_CATEGORY = 'female';
CATEGORY_NAME = 'name';
CATEGORY_FEMALE_EQUIVALENT_NAME = 'female-equivalent-' + CATEGORY_NAME;

LANGUAGE_JSON_FILE = '/files/data/website-language-variables.json';
LANGUAGE_MISSING_VARIABLE_SIGN = '!!!';
LANGUAGE_VARIABLE_EDIT_BUTTON = 'lang-edit-fields';
LANGUAGE_VARIABLE_HIDE_BUTTON = 'lang-hide-fields';
LANGUAGE_VARIABLE_REFRESH_BUTTON = 'lang-refresh-fields';

let languageVariables = {};
let patronCategories = {};
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

const getCardOwnerInputValue = function() {
  return document.getElementById('card-owner').value;
}

const getCanvas = function() {
  return document.getElementById('cards-canvas');
}

const getContext = function() {
  const canvas = getCanvas();

  return canvas.getContext('2d');
}

const buildForm = async function() {
  languageVariables = await getJsonFromFile(LANGUAGE_JSON_FILE);
  patronCategories = await getJsonFromFile(PATRON_CATEGORIES_JSON_FILE);
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
    option.innerHTML = getLanguageVariable(value);
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
  const context = getContext();

  canvas.width = width;
  canvas.height = height;

  canvas.style.width = canvas.style.width || canvas.width + 'px';
  canvas.style.height = canvas.style.height || canvas.height + 'px';

  var scaleFactor = CANVAS_SCALE_FACTOR;
  canvas.width = Math.ceil(canvas.width * scaleFactor);
  canvas.height = Math.ceil(canvas.height * scaleFactor);

  context.scale(scaleFactor, scaleFactor);
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

const drawEmptyRectangle = function(x, y, width, height, borderColor) {
  const context = getContext();

  context.lineWidth = 1;
  context.strokeStyle = borderColor;
  context.strokeRect(x, y, width, height);
}

const drawFilledRectangle = function(x, y, width, height, backgroundColor) {
  const context = getContext();

  context.fillStyle = backgroundColor;
  context.fillRect(x, y, width, height);
}

const getTriangleHeight = function(size) {
  return Math.sqrt(3 / 4 * size * size);
}

const drawBorderedAndFilledTriangle = function(x, y, size, borderColor, backgroundColor) {
  const context = getContext();

  const h = getTriangleHeight(size);

  context.beginPath();
  context.moveTo(x, y + h);
  context.lineTo(x + size, y + h);
  context.lineTo(x + size / 2, y);
  context.closePath();

  context.lineWidth = 1;
  context.strokeStyle = borderColor;
  context.stroke();

  context.fillStyle = backgroundColor;
  context.fill();
}

const drawBorderedAndFilledTetragon = function(x1, y1, x2, y2, x3, y3, x4, y4, borderColor, backgroundColor) {
  const context = getContext();

  context.beginPath();
  context.moveTo(x1, y1);
  context.lineTo(x2, y2);
  context.lineTo(x3, y3);
  context.lineTo(x4, y4);
  context.closePath();

  context.lineWidth = 1;
  context.strokeStyle = borderColor;
  context.stroke();

  context.fillStyle = backgroundColor;
  context.fill();
}

const drawBorderedAndFilledCircle = function(x, y, r, borderColor, backgroundColor) {
  context.beginPath();
  context.lineWidth = 1;
  context.strokeStyle = borderColor;
  context.arc(x, y, r, 0, 2 * Math.PI);
  context.stroke();

  context.fillStyle = backgroundColor;
  context.fill();
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
  newWindow.document.write('<img src="' + canvas.toDataURL() + '" width="100%" height="100%">');
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
  result[CARD_DATA_FIELD_REFRESHED_PARAMS_PATH] = '';
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

const getLanguageVariable = function(variable, capitalize) {
  let result = LANGUAGE_MISSING_VARIABLE_SIGN;

  const variableTranslations = languageVariables[variable];
  if (variableTranslations === undefined) {
    return result;
  }

  let language = getLanguage();
  let translation = variableTranslations[language];
  let foundLanguageTranslation = true;

  if (translation === undefined) {
    foundLanguageTranslation = false;
    for (language in variableTranslations) {
      result = variableTranslations[language];
      break;
    }
  } else {
    result = translation;
  }

  if (capitalize) {
    result = result.charAt(0).toUpperCase() + result.slice(1);
  }

  if (!foundLanguageTranslation) {
    result += ' [' + language + ']';
  }

  return result;
}

const getCategoriesLanguageVariables = function(categories) {
  let result = [];
  let language = getLanguage();
  let isFemale = false;
  if (categories.indexOf(FEMALE_CATEGORY) != -1) {
    isFemale = true;
  }

  for (const category of categories) {
    let translation = category + LANGUAGE_MISSING_VARIABLE_SIGN;

    if (isFemale && patronCategories[category][CATEGORY_FEMALE_EQUIVALENT_NAME] != undefined) {
      if (patronCategories[category][CATEGORY_FEMALE_EQUIVALENT_NAME][language] != undefined) {
        translation = patronCategories[category][CATEGORY_FEMALE_EQUIVALENT_NAME][language];
      }
    } else if (patronCategories[category][CATEGORY_NAME] != undefined) {
      if (patronCategories[category][CATEGORY_NAME][language] != undefined) {
        translation = patronCategories[category][CATEGORY_NAME][language];
      }
    }

    result.push(translation);
  }

  return result;
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

const addHrChildElement = function(element) {
  let hr = document.createElement('hr');
  element.appendChild(hr);
}

const addBrChildElement = function(element) {
  let br = document.createElement('br');
  element.appendChild(br);
}

const addSpanChildElement = function(element, text) {
  let span = document.createElement('span');
  span.innerHTML = text;
  element.appendChild(span);
}

const addInputChildElement = function(element, name, value) {
  let input = document.createElement('input');
  input.name = name;
  input.value = value;
  element.appendChild(input);
}

const addButtonChildElement = function(element, name, onClickFunction) {
  let button = document.createElement('button');
  button.onclick = onClickFunction;
  button.innerHTML = name;
  element.appendChild(button);
}

const addInputFieldsFormDivChildElement = function(element, cardId, cardDataParams) {
  const divId = CARD_FORM_INPUTS_DIV_ID_PREFIX + cardId;

  let div = document.createElement('div');
  div.id = divId;
  div.style = CSS_INVISIBLE;

  addButtonChildElement(div, getLanguageVariable(LANGUAGE_VARIABLE_HIDE_BUTTON, true), function() {
    document.getElementById(divId).style = CSS_INVISIBLE;
  });
  for (const field in cardDataParams) {
    addBrChildElement(div);
    addSpanChildElement(div, getLanguageVariable(field, true) + ': ');
    addInputChildElement(div, field, cardDataParams[field]);
  }
  addBrChildElement(div);
  addButtonChildElement(div, getLanguageVariable(LANGUAGE_VARIABLE_REFRESH_BUTTON, true), function() {
    saveInputValuesIntoCardParams(cardId);
    drawCard(cardId);
  });
  addHrChildElement(div);

  element.appendChild(div);
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
  addSpanChildElement(div, '&nbsp;&nbsp;&nbsp;');
  addButtonChildElement(div, getLanguageVariable(LANGUAGE_VARIABLE_EDIT_BUTTON, true), function() {
    const divId = CARD_FORM_INPUTS_DIV_ID_PREFIX + cardId;
    document.getElementById(divId).style = '';
  });
  addInputFieldsFormDivChildElement(div, cardId, cardData[CARD_DATA_FIELD_PARAMS]);
}

const saveInputValuesIntoCardParams = function(cardId) {
  const div = document.getElementById(CARD_FORM_INPUTS_DIV_ID_PREFIX + cardId);
  for (const child of div.childNodes) {
    if (child.tagName === 'INPUT') {
      cardsData[cardId][CARD_DATA_FIELD_PARAMS][child.name] = child.value;
    }
  }
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

const drawImage = function(imageData, x, y, width, height, onLoadFunction) {
  const imageUrl = imageData[IMAGE_DATA_FIELD_FILE_URL];
  const imageAreaTopLeftX = imageData[IMAGE_DATA_FIELD_AREA_TOP_LEFT_X];
  const imageAreaTopLeftY = imageData[IMAGE_DATA_FIELD_AREA_TOP_LEFT_Y];
  const imageAreaBottomRightX = imageData[IMAGE_DATA_FIELD_AREA_BOTTOM_RIGHT_X];
  const imageAreaBottomRightY = imageData[IMAGE_DATA_FIELD_AREA_BOTTOM_RIGHT_Y];

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
    onLoadFunction();
  }

  image.src = imageUrl;
}

const drawQrCode = function(url, x, y, size, darkColor, lightColor) {
  const context = getContext();
  const divElement = document.createElement('div');

  const options = {
    width: size,
    height: size,
    colorDark : darkColor,
    colorLight : lightColor,
    correctLevel : QRCode.CorrectLevel.L
  };
  let qrCode = new QRCode(divElement, options);
  qrCode.makeCode(url);

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

const getDataFileParams = function(cardType, data, dataPath) {
  let result = {};

  if (cardType === CARD_TYPE_PATRONS || cardType === CARD_TYPE_GOD) {
    const cardOwner = getCardOwnerInputValue();
    const nameData = getTranslatedNameData(data, FILE_DATA_NAMES_KEY);
    const imageData = data[FILE_DATA_IMAGES_KEY]['1'];
    const qrCodeUrl = getProtocol() + '//' + getHostname() + '/' + dataPath;

    result[CARD_DATA_PARAMS_FIELD_NAME] = nameData[1];
    result[CARD_DATA_PARAMS_FIELD_LANGUAGE] = nameData[2];
    result[CARD_DATA_PARAMS_FIELD_IMAGE_FILE_URL] = imageData[IMAGE_DATA_FIELD_FILE_URL];
    result[CARD_DATA_PARAMS_FIELD_IMAGE_AREA_TOP_LEFT_X] = imageData[IMAGE_DATA_FIELD_AREA_TOP_LEFT_X];
    result[CARD_DATA_PARAMS_FIELD_IMAGE_AREA_TOP_LEFT_Y] = imageData[IMAGE_DATA_FIELD_AREA_TOP_LEFT_Y];
    result[CARD_DATA_PARAMS_FIELD_IMAGE_AREA_BOTTOM_RIGHT_X] = imageData[IMAGE_DATA_FIELD_AREA_BOTTOM_RIGHT_X];
    result[CARD_DATA_PARAMS_FIELD_IMAGE_AREA_BOTTOM_RIGHT_Y] = imageData[IMAGE_DATA_FIELD_AREA_BOTTOM_RIGHT_Y];
    result[CARD_DATA_PARAMS_FIELD_DEATH] = getDeathDate(data[FILE_DATA_DEATH_KEY]);
    result[CARD_DATA_PARAMS_FIELD_CATEGORIES] = data[FILE_DATA_CATEGORIES_KEY] ? getCategoriesLanguageVariables(data[FILE_DATA_CATEGORIES_KEY]).join(', ') : '';
    result[CARD_DATA_PARAMS_FIELD_ORDER] = data[FILE_DATA_ORDER_KEY] ? data[FILE_DATA_ORDER_KEY].join(', ') : '';
    result[CARD_DATA_PARAMS_FIELD_CARD_OWNER] = cardOwner;
    result[CARD_DATA_PARAMS_FIELD_QR_CODE_URL] = qrCodeUrl;
  }

  return result;
}

const getDeathDate = function(dates) {
  if (dates.length === 0) {
    return UNKNOWN;
  } else if (dates.length === 1) {
    return dates[0];
  }

  let showMonthAndDay = true;
  let prevMonthAndDay = null;
  let date;
  for (date of dates) {
    const monthAndDay = date.replace(/^.+(-[0-1][0-9]-[0-3][0-9])$/, '$1');
    if (showMonthAndDay) {
      if (monthAndDay !== date) {
        if (prevMonthAndDay === null) {
          prevMonthAndDay = monthAndDay;
        } else if (prevMonthAndDay !== monthAndDay) {
          showMonthAndDay = false;
        }
      } else {
        showMonthAndDay = false;
      }
    }
  }
  if (!showMonthAndDay) {
    date = date.replace(/^([~<>-]*[0-9]+)-.+$/, '$1');
  }

  if (!isNaN(date[0]) && !isNaN(parseInt(date[0]))) {
    return '~' + date;
  }

  return date;
}

const drawGodTriangle = function(x, y, size) {
  const context = getContext();

  //drawBorderedAndFilledTriangle(x, y, size, 'black', 'white');

  let image = new Image();
  image.onload = function() {
    context.drawImage(image, x, y, size, size);
  }

  image.src = TRINITY_SYMBOL_URL;
}

const drawStatusTriangle = function(x, y, size) {
  const height = getTriangleHeight(size);

  const smallTriangleCoeff = 1/3.25;
  const circleSizeCoeff = 1/21;
  const circleSize = size * circleSizeCoeff;

  const smallTriangleSize = size * smallTriangleCoeff;
  const smallTriangleHeight = height * smallTriangleCoeff;

  const mediumTriangleHeight = height - 1.5 * smallTriangleHeight;
  const mediumTriangleSize = size - 1.5 * smallTriangleSize;

  const mediumTriangleTopX = x + size / 2;
  const mediumTriangleTopY = y + smallTriangleHeight;
  const mediumTriangleLeftX = mediumTriangleTopX - mediumTriangleSize / 2;
  const mediumTriangleLeftY = y + height - smallTriangleHeight / 2;
  const mediumTriangleRightX = mediumTriangleTopX + mediumTriangleSize / 2;
  const mediumTriangleRightY = mediumTriangleLeftY;

  const smallSideTrianglesCircleMoveX = smallTriangleSize / 2.7;
  const whiteTriangleY = mediumTriangleLeftY - 1.5 * smallTriangleHeight;

  drawBorderedAndFilledTriangle(x, y, size, 'white', STATUS_COLOR_BORDER);

  drawBorderedAndFilledTriangle(x + size / 2 - smallTriangleSize / 2, y, smallTriangleSize, STATUS_COLOR_BORDER, STATUS_COLOR_YELLOW);
  drawBorderedAndFilledTriangle(x, y + height - smallTriangleHeight , smallTriangleSize, STATUS_COLOR_BORDER, STATUS_COLOR_BLUE);
  drawBorderedAndFilledTriangle(x + size - smallTriangleSize, y + height - smallTriangleHeight , smallTriangleSize, STATUS_COLOR_BORDER, STATUS_COLOR_RED);

  //green tetragon
  drawBorderedAndFilledTetragon(
    mediumTriangleTopX, mediumTriangleTopY,
    mediumTriangleLeftX, mediumTriangleLeftY,
    x + smallTriangleSize / 2, y + height - smallTriangleHeight,
    mediumTriangleTopX - smallTriangleSize / 2, mediumTriangleTopY,
    STATUS_COLOR_BORDER, STATUS_COLOR_GREEN
  );
  //orange tetragon
  drawBorderedAndFilledTetragon(
    mediumTriangleTopX, mediumTriangleTopY,
    mediumTriangleRightX, mediumTriangleRightY,
    x + size - smallTriangleSize / 2, y + height - smallTriangleHeight,
    mediumTriangleTopX + smallTriangleSize / 2, mediumTriangleTopY,
    STATUS_COLOR_BORDER, STATUS_COLOR_ORANGE
  );
  //violet tetragon
  drawBorderedAndFilledTetragon(
    mediumTriangleLeftX, mediumTriangleLeftY,
    mediumTriangleRightX, mediumTriangleRightY,
    x + size - smallTriangleSize, y + height,
    x + smallTriangleSize, y + height,
    STATUS_COLOR_BORDER, STATUS_COLOR_VIOLET
  );

  //indigo triangle
  drawBorderedAndFilledTriangle(mediumTriangleTopX - mediumTriangleSize / 2, mediumTriangleTopY, mediumTriangleSize, STATUS_COLOR_BORDER, STATUS_COLOR_INDIGO);
  //white triangle
  drawBorderedAndFilledTriangle(x + size / 2 - smallTriangleSize / 2, whiteTriangleY, smallTriangleSize, STATUS_COLOR_BORDER, STATUS_COLOR_WHITE);

  //yellow circle
  drawBorderedAndFilledCircle(x + size / 2, y + smallTriangleHeight / 2 , circleSize, STATUS_COLOR_BORDER, STATUS_COLOR_CIRCLE);
  //blue circle
  drawBorderedAndFilledCircle(x + smallSideTrianglesCircleMoveX, y + height - smallTriangleHeight * 1 / 4 , circleSize, STATUS_COLOR_BORDER, STATUS_COLOR_CIRCLE);
  //red circle
  drawBorderedAndFilledCircle(x + size - smallSideTrianglesCircleMoveX, y + height - smallTriangleHeight * 1 / 4 , circleSize, STATUS_COLOR_BORDER, STATUS_COLOR_CIRCLE);

  //violet circle
  drawBorderedAndFilledCircle(x + size / 2, y + height - smallTriangleHeight / 4 , circleSize, STATUS_COLOR_BORDER, STATUS_COLOR_CIRCLE);
  //green circle
  drawBorderedAndFilledCircle(x + smallTriangleSize, whiteTriangleY + smallTriangleHeight / 2, circleSize, STATUS_COLOR_BORDER, STATUS_COLOR_CIRCLE);
  //orange circle
  drawBorderedAndFilledCircle(x + size - smallTriangleSize, whiteTriangleY + smallTriangleHeight / 2, circleSize, STATUS_COLOR_BORDER, STATUS_COLOR_CIRCLE);

  //indigo circle
  drawBorderedAndFilledCircle(x + size / 2, y + height - smallTriangleHeight * 3 / 4, circleSize, STATUS_COLOR_BORDER, STATUS_COLOR_CIRCLE);
  //white circle
  drawBorderedAndFilledCircle(x + size / 2, whiteTriangleY + smallTriangleHeight / 2, circleSize, STATUS_COLOR_BORDER, STATUS_COLOR_CIRCLE);
}

const drawPatronRank = function(x, y, width, height, columns, rows) {
  const columnSize = width / columns;
  const rowSize = height / rows;

  drawFilledRectangle(x, y, width, height, 'white');
  for (let row = 0; row < rows; row++) {
    for (let column = 0; column < columns; column++) {
      drawEmptyRectangle(x + column * columnSize, y + row * rowSize, columnSize, rowSize, PATRON_RANK_BORDER_COLOR);
    }
  }
}

const drawCardBackground = function(x, y, width, height, cardType, onLoadFunction) {
  const imageData = CARD_TYPE_BACKGROUND_IMAGES[cardType];

  drawFilledRectangle(x, y, width, height, CARD_BACKGROUND_COLOR);

  //drawImage(imageData, x, y, width, height, onLoadFunction);
  onLoadFunction();
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
    const dataPath = cardData[CARD_DATA_FIELD_PATH];
    const refreshedParamsPath = cardData[CARD_DATA_FIELD_REFRESHED_PARAMS_PATH];
    const cardType = dataPath.replace(/\/.*$/, '');

    const fontColor = DEFAULT_FONT_COLOR;
    const fontStyle = FONT_STYLE_NORMAL;
    const textAlign = TEXT_ALIGN_CENTER;

    let params = cardData[CARD_DATA_FIELD_PARAMS];
    if (refreshedParamsPath !== dataPath) {
      params = getDataFileParams(cardType, data, dataPath);
      cardData[CARD_DATA_FIELD_REFRESHED_PARAMS_PATH] = dataPath;
      cardData[CARD_DATA_FIELD_PARAMS] = params;
      cardsData[cardId] = cardData;
    }

    const marginSize = mm2px(3);

    //background
    drawCardBackground(x, y, cardWidth, cardHeight, cardType, function() {

      //name
      const nameWidth = cardWidth - 2 * marginSize;
      const nameHeight = mm2px(7);
      const nameX = x + marginSize;
      const nameY = y;
      const nameColor = 'white';
      if (params[CARD_DATA_PARAMS_FIELD_NAME] !== undefined) {
        drawText(params[CARD_DATA_PARAMS_FIELD_NAME], nameX, nameY, nameWidth, nameHeight, nameColor, fontStyle, textAlign);
      }

      //image
      const imageSize = mm2px(36);
      const imageWidth = imageSize;
      const imageHeight = imageSize;
      const imageX = x + marginSize;
      const imageY = y + nameHeight;
      const imageData = {
        [IMAGE_DATA_FIELD_FILE_URL]: params[CARD_DATA_PARAMS_FIELD_IMAGE_FILE_URL],
        [IMAGE_DATA_FIELD_AREA_TOP_LEFT_X]: params[CARD_DATA_PARAMS_FIELD_IMAGE_AREA_TOP_LEFT_X],
        [IMAGE_DATA_FIELD_AREA_TOP_LEFT_Y]: params[CARD_DATA_PARAMS_FIELD_IMAGE_AREA_TOP_LEFT_Y],
        [IMAGE_DATA_FIELD_AREA_BOTTOM_RIGHT_X]: params[CARD_DATA_PARAMS_FIELD_IMAGE_AREA_BOTTOM_RIGHT_X],
        [IMAGE_DATA_FIELD_AREA_BOTTOM_RIGHT_Y]: params[CARD_DATA_PARAMS_FIELD_IMAGE_AREA_BOTTOM_RIGHT_Y]
      }
      drawImage(imageData, imageX, imageY, imageWidth, imageHeight, function() {
      });

      //QR code
      const qrCodeSize = mm2px(20);
      const qrCodeX = x + cardWidth - qrCodeSize - marginSize;
      const qrCodeY = y + nameHeight + imageHeight - qrCodeSize;
      const qrCodeDarkColor = 'black';
      const qrCodeLightColor = 'white';
      drawQrCode(params[CARD_DATA_PARAMS_FIELD_QR_CODE_URL], qrCodeX, qrCodeY, qrCodeSize, qrCodeDarkColor, qrCodeLightColor);

      //language
      const languageWidth = mm2px(3);
      const languageHeight = mm2px(3);
      const languageX = x + cardWidth - languageWidth - marginSize;
      const languageY = y + nameHeight + imageHeight / 3;
      const languageColor = 'red';
      if (params[CARD_DATA_PARAMS_FIELD_LANGUAGE] !== undefined) {
        drawText(params[CARD_DATA_PARAMS_FIELD_LANGUAGE].toUpperCase(), languageX, languageY, languageWidth, languageHeight, languageColor, fontStyle, TEXT_ALIGN_JUSTIFY);
      }

      //project name
      const projectNameText = "Gratia Dei's My Patrons & Patrons Space";
      const projectNameWidth = cardWidth - 2 * marginSize;
      const projectNameHeight = mm2px(4);
      const projectNameX = x + marginSize;
      const projectNameY = y + cardHeight - projectNameHeight;
      const projectNameColor = 'yellow';
      drawText(projectNameText, projectNameX, projectNameY, projectNameWidth, projectNameHeight, projectNameColor, fontStyle, TEXT_ALIGN_JUSTIFY);

      //death
      const deathWidth = cardWidth - 2 * marginSize;
      const deathHeight = mm2px(7);
      const deathX = x + marginSize;
      const deathY = y + nameHeight + imageHeight;
      const deathColor = 'white';
      if (params[CARD_DATA_PARAMS_FIELD_DEATH] !== undefined) {
        drawText(params[CARD_DATA_PARAMS_FIELD_DEATH], deathX, deathY, deathWidth, deathHeight, deathColor, fontStyle, TEXT_ALIGN_LEFT);
      }

      //categories
      const categoriesWidth = cardWidth / 2;
      const categoriesHeight = mm2px(5);
      const categoriesX = x + marginSize;
      const categoriesY = y + nameHeight + imageHeight + deathHeight;
      const categoriesColor = 'green';
      if (params[CARD_DATA_PARAMS_FIELD_CATEGORIES] !== undefined) {
        drawText(params[CARD_DATA_PARAMS_FIELD_CATEGORIES], categoriesX, categoriesY, categoriesWidth, categoriesHeight, categoriesColor, fontStyle, TEXT_ALIGN_LEFT);
      }

      //order
      const orderWidth = cardWidth / 2;
      const orderHeight = mm2px(7);
      const orderX = x + marginSize;
      const orderY = categoriesY + categoriesHeight;
      const orderColor = 'yellow';
      if (params[CARD_DATA_PARAMS_FIELD_ORDER] !== undefined) {
        drawText(params[CARD_DATA_PARAMS_FIELD_ORDER].replace(/,([^ ])/g, ', $1'), orderX, orderY, orderWidth, orderHeight, orderColor, fontStyle, TEXT_ALIGN_LEFT);
      }

      //God symbol or patron status
      const triangleSize = mm2px(30);
      const triangleX = x + cardWidth - marginSize - triangleSize;
      const triangleY = qrCodeY + qrCodeSize;
      if (cardType === CARD_TYPE_GOD) {
        drawGodTriangle(triangleX, triangleY, triangleSize);
      } else if (cardType === CARD_TYPE_PATRONS) {
        drawStatusTriangle(triangleX, triangleY, triangleSize);
      }

      //patron rank
      const rankWidth = mm2px(PATRON_RANK_COLUMNS * PATRON_RANK_CELL_SIZE);
      const rankHeight = mm2px(PATRON_RANK_ROWS * PATRON_RANK_CELL_SIZE);
      const rankX = x + marginSize;
      const rankY = orderY + orderHeight;
      drawPatronRank(rankX, rankY, rankWidth, rankHeight, PATRON_RANK_COLUMNS, PATRON_RANK_ROWS);

      //card owner
      const cardOwnerWidth = (cardWidth + marginSize) / 2;
      const cardOwnerHeight = mm2px(7);
      const cardOwnerX = x + cardWidth / 2 - marginSize;
      const cardOwnerY = triangleY + triangleSize;
      const cardOwnerColor = 'black';
      drawFilledRectangle(cardOwnerX, cardOwnerY, cardOwnerWidth, cardOwnerHeight, 'white');
      drawText(params[CARD_DATA_PARAMS_FIELD_CARD_OWNER], cardOwnerX, cardOwnerY, cardOwnerWidth, cardOwnerHeight, cardOwnerColor, fontStyle, TEXT_ALIGN_CENTER);
    });
  }
}

buildForm();
