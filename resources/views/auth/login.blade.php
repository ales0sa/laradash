@extends('Dashboard::layouts.dashboard')

@section('content')
<style type="text/css">* {
  font-family: -apple-system, BlinkMacSystemFont, "San Francisco", Helvetica, Arial, sans-serif;
  font-weight: 300;
  margin: 0;
}

html, body {
  height: 100vh; 
  width: 100vw;
  margin: 0 0;
  display: flex;
  align-items: flex-start;
  justify-content: flex-start;
  background: #f3f2f2;
}

h4 {
  font-size: 24px;
  font-weight: 600;
  color: #000;
  opacity: 0.85;
}

label {
  font-size: 12.5px;
  color: #000;
  opacity: 0.8;
  font-weight: 400;
}

form {
  padding: 40px 30px;
  background: #fefefe;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  padding-bottom: 20px;
  width: 300px;
}
form h4 {
  margin-bottom: 20px;
  color: rgba(0, 0, 0, 0.5);
}
form h4 span {
  color: black;
  font-weight: 700;
}
form p {
  line-height: 155%;
  margin-bottom: 5px;
  font-size: 14px;
  color: #000;
  opacity: 0.65;
  font-weight: 400;
  max-width: 200px;
  margin-bottom: 40px;
}

a.discrete {
  color: rgba(0, 0, 0, 0.4);
  font-size: 14px;
  border-bottom: solid 1px rgba(0, 0, 0, 0);
  padding-bottom: 4px;
  margin-left: auto;
  font-weight: 300;
  transition: all 0.3s ease;
  margin-top: 40px;
}
a.discrete:hover {
  border-bottom: solid 1px rgba(0, 0, 0, 0.2);
}

button {
  -webkit-appearance: none;
  width: auto;
  min-width: 100px;
  border-radius: 24px;
  text-align: center;
  padding: 15px 40px;
  margin-top: 5px;
  background-color: #000;
  color: #fff;
  font-size: 14px;
  margin-left: auto;
  font-weight: 500;
  box-shadow: 0px 2px 6px -1px rgba(0, 0, 0, 0.13);
  border: none;
  transition: all 0.3s ease;
  outline: 0;
}
button:hover {
  transform: translateY(-3px);
  box-shadow: 0 2px 6px -1px rgba(182, 157, 230, 0.65);
}
button:hover:active {
  transform: scale(0.99);
}

input {
  font-size: 16px;
  padding: 20px 0px;
  height: 56px;
  border: none;
  border-bottom: solid 1px rgba(0, 0, 0, 0.1);
  background: #fff;
  width: 280px;
  box-sizing: border-box;
  transition: all 0.3s linear;
  color: #000;
  font-weight: 400;
  -webkit-appearance: none;
}
input:focus {
  border-bottom: solid 1px #000;
  outline: 0;
  box-shadow: 0 2px 6px -8px rgba(182, 157, 230, 0.45);
}

.floating-label {
  position: relative;
  margin-bottom: 10px;
  width: 100%;
}
.floating-label label {
  position: absolute;
  top: calc(50% - 7px);
  left: 0;
  opacity: 0;
  transition: all 0.3s ease;
  padding-left: 44px;
}
.floating-label input {
  width: calc(100% - 44px);
  margin-left: auto;
  display: flex;
}
.floating-label .icon {
  position: absolute;
  top: 0;
  left: 0;
  height: 56px;
  width: 44px;
  display: flex;
}
.floating-label .icon svg {
  height: 30px;
  width: 30px;
  margin: auto;
  opacity: 0.15;
  transition: all 0.3s ease;
}
.floating-label .icon svg path {
  transition: all 0.3s ease;
}
.floating-label input:not(:-moz-placeholder-shown) {
  padding: 28px 0px 12px 0px;
}
.floating-label input:not(:-ms-input-placeholder) {
  padding: 28px 0px 12px 0px;
}
.floating-label input:not(:placeholder-shown) {
  padding: 28px 0px 12px 0px;
}
.floating-label input:not(:-moz-placeholder-shown) + label {
  transform: translateY(-10px);
  opacity: 0.7;
}
.floating-label input:not(:-ms-input-placeholder) + label {
  transform: translateY(-10px);
  opacity: 0.7;
}
.floating-label input:not(:placeholder-shown) + label {
  transform: translateY(-10px);
  opacity: 0.7;
}
.floating-label input:valid:not(:-moz-placeholder-shown) + label + .icon svg {
  opacity: 1;
}
.floating-label input:valid:not(:-ms-input-placeholder) + label + .icon svg {
  opacity: 1;
}
.floating-label input:valid:not(:placeholder-shown) + label + .icon svg {
  opacity: 1;
}
.floating-label input:valid:not(:-moz-placeholder-shown) + label + .icon svg path {
  fill: #000;
}
.floating-label input:valid:not(:-ms-input-placeholder) + label + .icon svg path {
  fill: #000;
}
.floating-label input:valid:not(:placeholder-shown) + label + .icon svg path {
  fill: #000;
}
.floating-label input:not(:valid):not(:focus) + label + .icon {
  -webkit-animation-name: shake-shake;
          animation-name: shake-shake;
  -webkit-animation-duration: 0.3s;
          animation-duration: 0.3s;
}

@-webkit-keyframes shake-shake {
  0% {
    transform: translateX(-3px);
  }
  20% {
    transform: translateX(3px);
  }
  40% {
    transform: translateX(-3px);
  }
  60% {
    transform: translateX(3px);
  }
  80% {
    transform: translateX(-3px);
  }
  100% {
    transform: translateX(0px);
  }
}

@keyframes shake-shake {
  0% {
    transform: translateX(-3px);
  }
  20% {
    transform: translateX(3px);
  }
  40% {
    transform: translateX(-3px);
  }
  60% {
    transform: translateX(3px);
  }
  80% {
    transform: translateX(-3px);
  }
  100% {
    transform: translateX(0px);
  }
}
.session {
  display: flex;
  flex-direction: row;
  width: auto;
  height: auto;
  margin: auto auto;
  background: #ffffff;
  border-radius: 4px;
  box-shadow: 0px 2px 6px -1px rgba(0, 0, 0, 0.12);
}

.left {
  width: 220px;
  height: auto;
  min-height: 100%;
  position: relative;
  background-color: #fff;
  background-image: url('/logo.png');
  background-size: 170px;
  background-repeat: no-repeat;
  background-position: center;
  border-top-left-radius: 4px;
  border-bottom-left-radius: 4px;
}
.left svg {
  height: 40px;
  width: auto;
  margin: 20px;
}
</style>
  <div class="session">
    <div class="left">

    </div>
    <form action="/adm/login" class="log-in" autocomplete="off" method="post"> 
    	@csrf
      <h4>Acceso al <span>Panel</span></h4>


      @if(Session::has('errors'))
      <div style="background: danger;"><small> Datos incorrectos de ingreso. </small> </div>
      @else
          <p>Bienvenido! Ingresa tus credenciales para continuar.</p>
      @endif


      <div class="floating-label">
        <input placeholder="Usuario" type="text" name="username" id="email" autocomplete="off">
        <label for="email">Usuario</label>
        <div class="icon">

        </div>
      </div>
      <div class="floating-label">
        <input placeholder="Password" type="password" name="password" id="password" autocomplete="off">
        <label for="password">Password:</label>
        <div class="icon">
        </div>
        
      </div>
      <button type="submit" >Ingresar</button>

    </form>
  </div>

@endsection