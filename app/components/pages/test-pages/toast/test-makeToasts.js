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
}, 3000); 

// set a random time between 2 and 30 seconds
function getRandomTime() {
  return Math.floor(Math.random() * (30000 - 2000 + 1)) + 2000;
}