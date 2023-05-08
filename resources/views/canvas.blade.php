<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        .container {
  display: flex;
  flex-direction:column;
}
    </style>
</head>
<body>
    <div class="container">
        <div>
          <canvas id="canvas" width="500" height="500"></canvas>
          <div style="position:absolute;top:12%;left:43%;">Choose Color</div>
        <div style="position:absolute;top:15%;left:45%;width:10px;height:10px;background:green;" id="green" onclick="color(this)" class="co"></div>
        <div style="position:absolute;top:15%;left:46%;width:10px;height:10px;background:blue;" id="blue" onclick="color(this)" class="co"></div>
        <div style="position:absolute;top:15%;left:47%;width:10px;height:10px;background:red;" id="red" onclick="color(this)" class="co"></div>
        <div style="position:absolute;top:17%;left:45%;width:10px;height:10px;background:yellow;" id="yellow" onclick="color(this)" class="co"></div>
        <div style="position:absolute;top:17%;left:46%;width:10px;height:10px;background:orange;" id="orange" onclick="color(this)" class="co"></div>
        <div style="position:absolute;top:17%;left:47%;width:10px;height:10px;background:black;" id="black" onclick="color(this)" class="co"></div>
        {{-- <div style="position:absolute;top:20%;left:43%;">Eraser</div>
        <div style="position:absolute;top:23%;left:45%;width:15px;height:15px;background:white;border:2px solid;" id="white" onclick="color(this)" class="co"></div> --}}
        </div>
        <div>
          <button id="save" type="button">
            save
          </button>
          <button id="clear" type="button">
            clear
          </button>
          <input
            type="file"
            id="load"
            name="avatar"
            accept="image/png"
          >
        </div>
    </div>
</body>
<script type="text/javascript">
let co = 'black';
let a= 2;
let b= 2;
let test ='';
window.onload = () => {
  const canvas = document.getElementById('canvas');
  const saveButton = document.getElementById('save');
  const clearButton = document.getElementById('clear');
  const loadInput = document.getElementById('load');

  new Drawing(canvas, saveButton,clearButton, loadInput);
};

class Drawing {
  constructor(canvas, saveButton,clearButton, loadInput) {
    this.isDrawing = false;

    canvas.addEventListener('mousedown', (event) => this.startDrawing(event));
    canvas.addEventListener('mousemove', (event) => this.draw(event));
    canvas.addEventListener('mouseup', () => this.stopDrawing());

    saveButton.addEventListener('click', () => this.save());
    clearButton.addEventListener('click', () => this.clear());
    loadInput.addEventListener('change', (event) => this.load(event));

    const rect = canvas.getBoundingClientRect();

    this.offsetLeft = rect.left;
    this.offsetTop = rect.top;

    this.canvas = canvas;
    this.context = this.canvas.getContext('2d');
  }
  startDrawing(event) {

    this.isDrawing = true;
    this.context.beginPath();
    this.context.fillStyle = co;
    this.context.fillRect(event.clientX - this.offsetLeft, event.clientY - this.offsetTop, 4, 4);
    this.context.closePath();

  }
  stopDrawing() {
    this.isDrawing = false;
  }
  draw(event) {
    if (this.isDrawing) {
      this.context.fillRect(event.pageX - this.offsetLeft, event.pageY - this.offsetTop, a, b);
    }
  }
  save() {
    const data = this.canvas.toDataURL('image/png');
    const a = document.createElement('a');
    a.href = data;
    a.download = 'image.png';
    a.click();
  }
  clear(){
    var m = confirm("Want to clear");
            if (m) {
                const canvas = this.canvas;
                const context = canvas.getContext('2d');
                context.clearRect(0, 0, canvas.width, canvas.height);
            }
  }

  load(event) {
    const file = [...event.target.files].pop();
    this.readTheFile(file)
      .then((image) => this.loadTheImage(image))
    //   alert('hello');
  }
  loadTheImage(image) {
    const img = new Image();
    const canvas = this.canvas;
    img.onload = function () {
      const context = canvas.getContext('2d');
      context.clearRect(0, 0, canvas.width, canvas.height);
      context.drawImage(img, 0, 0);
    };
    img.src = image;
  }
  readTheFile(file) {
    const reader = new FileReader();
    return new Promise((resolve) => {
      reader.onload = (event) => {
        resolve(event.target.result);
      };
      reader.readAsDataURL(file);
    })
  }
}
function color(obj) {
switch (obj.id) {
    case "green":
        co = "green";
        break;
    case "blue":
        co = "blue";
        break;
    case "red":
        co = "red";
        break;
    case "yellow":
        co = "yellow";
        break;
    case "orange":
        co = "orange";
        break;
    case "black":
        co = "black";
        break;
    case "white":
        co = "white";
        break;
}
}


</script>
</html>
