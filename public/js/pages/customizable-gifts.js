//chars limit
const CHARS_LIMIT = 18;

// shapes block vars
const shapesBlock = $("#shapes_block");

// icons block vars
const iconsBlock = $("#icons_block");

// zodiac block vars
const zodiacSignsBlock = $("#zodiac_block");

//text block vars
const textBlock = $("#text_block");
const textInput = textBlock.find("input[name=shape-text]");
const charsLimitText = textBlock.find("#shape_chars_limit");

$(document).ready(function () {
  // set chars limit
  setCharsLimitHandler();

  textInput.on("keyup", function () {
    let length = $(this).val().length;
    length = CHARS_LIMIT - length;
    charsLimitText.text(length);
  });
});

const setCharsLimitHandler = () => {
  textInput.attr("maxlength", CHARS_LIMIT);
  charsLimitText.text(CHARS_LIMIT);
};
