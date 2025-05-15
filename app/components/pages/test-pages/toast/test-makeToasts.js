setInterval(() => {
  const messages = [
    [ 'Warning Message' , 'warning'],
    [ 'Error Message' , 'error'],
    [ 'Success Message' , 'success'],
    [ 'Info Message' , 'info'],
    [ 'Default Message' , null],
  ]
  const rand = Math.floor(Math.random() * messages.length);
  ToastComponent.newToast(messages[rand][0], getRandomTime(), messages[rand][1]);
}, 2000); 

// set a random time between 2 and 30 seconds
function getRandomTime() {
  return Math.floor(Math.random() * (10000 - 5000 + 1)) + 5000;
}