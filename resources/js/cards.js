/**
 * A4 format full size: 210mm x 297mm
 * printable area: 170mm x 247mm
 */
const PRINT_PPI = 96;
const CARD_WIDTH_IN_MILIMETERS = 63.5;
const CARD_HEIGHT_IN_MILIMETERS = 88;
const CARD_COLUMNS = 4;
const CARD_ROWS = 2;
const PRINTING_AREA_WIDTH_IN_MILIMETERS = CARD_WIDTH_IN_MILIMETERS * CARD_COLUMNS;
const PRINTING_AREA_HEIGHT_IN_MILIMETERS = CARD_HEIGHT_IN_MILIMETERS * CARD_ROWS;

const mm2px = function(milimeters) {
  const pixels = milimeters * PRINT_PPI / 25.4;

  return Math.round(pixels);
}

const getCanvas = function() {
  return document.getElementById('cards-canvas');
}

const getContext = function() {
  const canvas = getCanvas();

  return canvas.getContext('2d');
}

const scaleCanvas = function() {
  var canvas = getCanvas();

  canvas.width = mm2px(PRINTING_AREA_WIDTH_IN_MILIMETERS);
  canvas.height = mm2px(PRINTING_AREA_HEIGHT_IN_MILIMETERS);
}

const printCanvas = function() {
  const canvas = getCanvas();

  window.document.write('<img src="' + canvas.toDataURL() + '" />');
  window.print();
  window.location.reload();
}

const clearCanvas = function() {
  const canvas = getCanvas();
  const context = getContext();

  context.clearRect(0, 0, canvas.width, canvas.height);
}

const drawPrintableArea = function() {
  const context = getContext();

  const width = mm2px(PRINTING_AREA_WIDTH_IN_MILIMETERS);
  const height = mm2px(PRINTING_AREA_HEIGHT_IN_MILIMETERS);
  const x = 0;
  const y = 0;

  context.lineWidth = 1;
  context.strokeStyle = "black";
  context.strokeRect(x, y, width, height);
}

const drawCard = function(cardId, patronId) {
  if (cardId < 1 || cardId > CARD_COLUMNS * CARD_ROWS || !Number.isInteger(cardId)) {
    return;
  }

  const context = getContext();

  const width = mm2px(CARD_WIDTH_IN_MILIMETERS);
  const height = mm2px(CARD_HEIGHT_IN_MILIMETERS);
  const x = mm2px(CARD_WIDTH_IN_MILIMETERS * ((cardId - 1) % CARD_COLUMNS));
  const y = mm2px(CARD_HEIGHT_IN_MILIMETERS * Math.floor(cardId / CARD_COLUMNS));

  context.lineWidth = 1;
  context.strokeStyle = "black";
  context.strokeRect(x, y, width, height);
}

const buildCanvas = function() {
  scaleCanvas();
  clearCanvas();
  drawPrintableArea();
  drawCard(1, 0);
  drawCard(2, 0);
  drawCard(3, 0);
  drawCard(4, 0);
  drawCard(5, 0);
  drawCard(6, 0);
  drawCard(7, 0);
  drawCard(8, 0);
}

buildCanvas();
