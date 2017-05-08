function validarEntero(valor){ 
      //intento convertir a entero. 
      //si era un entero no le afecta, si no lo era lo intenta convertir 
       valor = parseInt(valor) 

      //Compruebo si es un valor num�rico 
      if (isNaN(valor)) { 
         //entonces (no es numero) devuelvo el valor cadena vacia 
         return "" 
      }else{ 
         //En caso contrario (Si era un n�mero) devuelvo el valor 
         return valor 
      } 
} 

function compruebaValidoEntero(){ 
   enteroValidado = validarEntero(document.f1.numero.value) 
   if (enteroValidado == ""){ 
      //si era la cadena vac�a es que no era v�lido. Lo aviso 
      if (!avisado){ 
         alert ("Debe escribir un entero!") 
         //selecciono el texto 
         document.f1.numero.select() 
         //coloco otra vez el foco 
         document.f1.numero.focus() 
         avisado=true 
         setTimeout('avisado=false',50) 
      } 
   }else 
      document.f1.numero.value = enteroValidado 
} 

function compruebaValidoCP(){ 
   CPValido=true 
   //si no tiene 5 caracteres no es v�lido 
   if (document.f1.codigo.value.length != 5) 
      CPValido=false 
   else{ 
      for (i=0;i<5;i++){ 
         CActual = document.f1.codigo.value.charAt(i) 
         if (validarEntero(CActual)==""){ 
            CPValido=false 
            break; 
         } 
      } 
   } 
   if (!CPValido){ 
      if (!avisado){ 
         //si no es valido, Lo aviso 
         alert ("Debe escribir un c�digo postal v�lido") 
         //selecciono el texto 
         document.f1.codigo.select() 
         //coloco otra vez el foco 
         //document.f1.codigo.focus() 
         avisado=true 
         setTimeout('avisado=false',50) 
      } 
   } 
} 
 
