import './bootstrap';

const hightlight = (process_name) => {
  switch(process_name){
    case "Tube Laser":
      return 'bg-blue-300'
    case "Outsourcing":
      return 'bg-yellow-300'
    case "Surface treatment":
      return 'bg-green-500'
    case "Quality controll":
      return 'bg-fuchsia-400'
    case "Welding":
      return 'bg-purple-500'
  }
}