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

const LINEAR_SCALE_SHORT_LINE_LENGTH = 0.5;
const CARD_WIDTH = 63.5;
const CARD_HEIGHT = 88;
const CARD_MARGIN = 3;
const CARD_ID_IDENTIFIER_TEXT_FONT_SIZE = 2;
const CARD_ID_IDENTIFIER_TEXT_Y_CHANGE = -0.5;
const CARD_ID_IDENTIFIER_TEXT_FONT_FAMILY = 'Arial';

const CARD_DATA_FIELD_X = 'x';
const CARD_DATA_FIELD_Y = 'y';
const CARD_DATA_FIELD_IS_ACTIVE = 'isActive';

const CARD_FORM_ID_PREFIX = 'card-form-';
const CARD_TYPE_ROOT_PATHS = {
  'god': '/files/data/records',
  'patrons': '/files/data/records'
};

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

  for (let value of formats) {
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

  for (let key in orientations) {
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

  for (let type of ALLOWED_PAPER_FORMAT_TYPES) {
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

const buildCanvas = function() {
  const size = getPrintableAreaSize();
  const width = mm2px(size.width);
  const height = mm2px(size.height);

  scaleCanvas(width, height);
  drawPreparedCanvasArea(width, height);
  calculateCardsCoordinatesAndActivity(width, height);
  for (let cardId in cardsData) {
    drawCard(cardId);
  }
  buildCardsForms();
}

const scaleCanvas = function(width, height) {
  const canvas = getCanvas();

  canvas.width = width;
  canvas.height = height;
}

const drawPreparedCanvasArea = function(width, height) {
  clearRectangle(0, 0, width, height);
  drawEmptyRectangle(0, 0, width, height);
  drawLinearScales(width, height);
}

const clearRectangle = function(x, y, width, height) {
  const context = getContext();

  context.clearRect(0, 0, width, height);
}

const drawEmptyRectangle = function(x, y, width, height) {
  const context = getContext();

  context.lineWidth = 1;
  context.strokeStyle = "black";
  context.strokeRect(x, y, width, height);
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

const setCardIdCoordinatesAndActivity = function(cardId, x, y) {
  if (cardsData[cardId] === undefined) {
    cardsData[cardId] = {};
  }

  cardsData[cardId][CARD_DATA_FIELD_IS_ACTIVE] = true;
  cardsData[cardId][CARD_DATA_FIELD_X] = x;
  cardsData[cardId][CARD_DATA_FIELD_Y] = y;
}

const setCardIdInactivity = function(cardId) {
  if (cardsData[cardId] === undefined) {
    cardsData[cardId] = {};
  }

  cardsData[cardId][CARD_DATA_FIELD_IS_ACTIVE] = false;
}

const drawCardIdIdentifierText = function(cardId, x, y) {
  context = getContext();

  context.font = mm2px(CARD_ID_IDENTIFIER_TEXT_FONT_SIZE) + 'px ' + CARD_ID_IDENTIFIER_TEXT_FONT_FAMILY;
  context.fillText(cardId + ':', x, y + mm2px(CARD_ID_IDENTIFIER_TEXT_Y_CHANGE));
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

  drawCardIdIdentifierText(cardId, x, y);
  drawEmptyRectangle(x, y, cardWidth, cardHeight);
}

const buildCardsForms = function() {
  let cardsFormsDiv = getCardsFormsDiv();

  for (let cardId in cardsData) {
    const cardFormId = CARD_FORM_ID_PREFIX + cardId;
    const cardData = cardsData[cardId];
    const div = document.getElementById(cardFormId);

    if (cardData[CARD_DATA_FIELD_IS_ACTIVE]) {
      if (div === null) {
        const subDiv = document.createElement('div');
        subDiv.id = cardFormId;
        cardsFormsDiv.appendChild(subDiv);

        buildCardFormElements(cardId);
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

const getLanguage = function() {
  const hostname = getHostname();

  return hostname.replace(/\..*$/, '');
}

const getTranslatedName = function(data, key) {
  const names = data[key];
  let language = getLanguage();

  if (names[language] === undefined) {
    language = Object.keys(names)[0];
  }

  const name = names[language];
  if (name instanceof Array) {
    return name.shift();
  }

  return name;
}

const addSpanChildElement = function(element, text) {
  let span = document.createElement('span');
  span.innerHTML = text;
  element.appendChild(span);
}

//const addSelectChildElement = function(element, id, options, selectedOption) {
  //let select = document.createElement('select');
  //select.id = id;

  //for (let value in options) {
    //let option = document.createElement('option');
    //option.value = value;
    //option.innerHTML = options[value];
    //if (value === selectedOption) {
      //option.selected = 'selected';
    //}

    //select.appendChild(option);
  //}

  //element.appendChild(select);
//}

const buildCardFormElements = async function(cardId) {
  const cardFormId = CARD_FORM_ID_PREFIX + cardId;
  const div = document.getElementById(cardFormId);

  //label
  addSpanChildElement(div, cardId + ': ');

  //card path selects
  //...todo

  //card input fields
  //...todo
}

buildForm();
