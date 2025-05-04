const pickerOptions = { 
  onEmojiSelect: console.log, 
  locale: 'pt',
  searchPosition: 'none' ,
  maxFrequentRows: 0 ,
  theme: 'auto' ,
  previewPosition:'none'
}

const picker = new EmojiMart.Picker(pickerOptions)
$("#content").append(picker)