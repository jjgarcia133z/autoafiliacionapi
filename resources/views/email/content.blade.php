<html>
<head>
<style>
        body {
          
            font-family: Arial, sans-serif;
        }
        div{
height: 65px;
left: 26.52%;
right: 59.18%;
top: calc(50% - 65px/2 + 90px);

font-family: 'Roboto';
font-style: normal;
font-weight: 500;
font-size: 20px;
line-height: 23px;
letter-spacing: 0.04px;

color: #2D4875;

        }
        .link{
            //styleName: Link/Link;
font-family: Montserrat;
font-size: 14px;
font-weight: 500;
line-height: 17px;
letter-spacing: 0px;
text-align: left;

font-family: Roboto;
font-size: 16px;
font-weight: 500;
line-height: 22px;
letter-spacing: 0.03999999910593033px;
text-align: left;


        }
        p{
height: 66px;
left: 26.46%;
right: 15%;
top: calc(50% - 66px/2 + 12.5px);

font-family: 'Roboto';
font-style: normal;
font-weight: 500;
font-size: 16px;
line-height: 22px;
letter-spacing: 0.04px;

/* Primary Blue/Primary - Blue - Main 500 */

color: #64676C;
        }

        h1 {
          

height: 43px;
left: 26.46%;
right: 8.12%;
top: calc(50% - 43px/2 - 107px);

font-family: 'Roboto';
font-style: normal;
font-weight: 500;
font-size: 32px;
line-height: 135.69%;
/* or 43px */

letter-spacing: 0.04px;

color: #2D4875;
        }
    </style>

    <title>Correo electrónico de prueba</title>
</head>
<body>

<img src="{{ asset('medi/medi.png') }}" alt="Descripción de la imagen">


    <h1>¡Hola, necesitamos validar tu cuenta!</h1>
    <p>Necesitamos validar que los datos ingresados son correctos, para hacerlo debés 
        validar el siguiente código de 6 <br>dígitos, el cuál debés activarlo en las próximas 2 horas, 
        pasado el tiempo el código deberá ser generado nuevamente</br> desde la auto afilicación. </>

<div>

<h3>Código de verificación:</h3>
<h3>{{$codigo}}</h3>
</div>

<p class="link">Ir a validar código www.medismart.net/login/Codeautentification</p>




    </body>
</html>

