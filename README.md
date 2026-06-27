# Silicon-Lalin

# Descripción del proyecto 
  Materiales Deza es una aplicación web diseñada para la gestión y la trazabilidad de materiales dentro de la empresa. 
  
  El sistema creado permite añadir materiales, eliminarlos o modificar sus estados a través de movimientos. También permite tramitar devoluciones y proporciona un sistema de rastreo, de esta forma, cualquier usuario autorizado en la plataforma podrá consultar el estado de un material y saber en todo momento cuál es su ubicación o persona a cargo actual. 
  
  La aplicación incluye un sistema de autenticación simple, diferenciando entre usuarios comunes y administradores, aquellos usuarios con el rol de "administrador" son quienes pueden validar una petición de devolución llevada a cabo sobre un material en específico, manteniéndose todo en el historial del propio material, por lo que no se pierde la información, facilitando así la trazabilidad del mismo.  
  
  El proyecto está desarrollado con Vue 3 en la parte frontend, PHP y MySQL/MariaDB en el backend, para su prueba se ha utilizado xampp como entorno local de ejecución. 

  # Instrucciones de instalación 
  Para poder instalar Materiales Deza en un entorno local, se requiere: 

  1. Clonar el repositorio de GitHub con el comando: git clone https://github.com/SarayP08/Silicon-Lalin.git.
  2. Instalar Xampp desde: https://www.apachefriends.org/download.html, activar PHP y MySQL, una vez hecho, añadir el directorio del proyecto en la siguiente ruta: C:\xampp\htdocs\.
  3. Desde el panel de configuración de PhpMyAdmin importar la base de datos schema. 
  4. Instalar node.js desde: https://nodejs.org/es/download, abrir el ejecutable y marcar la opción de instalar npm.
  5. Para comprobar la instalación, ejecuta los comandos: "node -v" y "npm -v". 
  6. Abrir la carpeta del proyecto en un IDE adecuado, (Visual Studio Code, PhpStorm, WebStorm, etc).
  7. Configurar dentro de la carpeta de backend/config el archivo de conexion.php en base a vuestra configuración.
  8. Desde la terminal (IDE o local) posicionarse en el directorio prueba-Silicon y ejecutar el comando: npm install.

  # Instrucciones de ejecución
  Para poder ejecutar Materiales Deza en un entorno local se debe hacer; 
  1. Abrir el panel de control de Xampp e iniciar los servicios Apache y MySQL.
  2. Desde una terminal, posicionarse en el directorio del frontend/prueba-Silicon.
  3. Ejecutar el servidor de desarrollo de Vue: npm run dev.
  4. El backend se ejecuta desde Apache en la carpeta ubicada en htdocs. 
  5. Abrir en el navegador la URL que muestra la terminal por defecto.
  6. Se ha creado un usuario administrador con el que podéis explorar la aplicación, sus credenciales son: {email: admin@gmail.com, password: 08012004.Saray}, en caso omiso, podéis registraros y cambiar vuestro rol desde el panel de PhPMyAdmin.

  # Descripción del diseño de la base de datos
  La base de datos utilizada para la aplicación se llama material_deza, y está formada por 6 tablas principales: 
  1. usuario.
  2. persona.
  3. ubicacion.
  4. material.
  5. movimiento_material.
  6. devolucion.
     
  La tabla de *usuario* almacena los datos de usuarios que se registran en la plataforma, tiene un rol, el cual permite diferenciar entre usuarios comunes y administradores.

  La tabla de *persona* almacena los datos de las personas que son registradas por los usuarios en el momento asignarle un material o moverlo. 

  La tabla de *ubicacion* almacena la ubicacion asignada a un material o a un nuevo movimiento.

  La tabla de *material*  representa los materiales que son gestionados en la plataforma, cada material tiene un identificador único y puede estar asociado a una persona o a una ubicación. 

  La tabla de *movimiento_material* es la tabla que traza todos los movimientos por los que pasa un material dentro de la plataforma, encargada de la trazabilidad de los mismos, cada vez que se genera un movimiento, se le asocia el material en cuestión, el origen y el destino y se recupera en un historial.

  La tabla de *devolucion* almacena las devoluciones que han sido validadas por los usuarios administradores, guardando la identificación del usuario, el material asociado, el movimiento y la fecha en que se validó la petición. 

  Las relaciones entre tablas se gestionan mediante claves foráneas. La tabla *material* se relaciona con *persona* y/o *ubicacion*, mientras que *movimiento_material* se relaciona con *material*, *persona*, *ubicacion* y *usuario*. Debido a que el objetivo principal de la página es mantener los datos claros y saber en todo momento dónde está cada material y en posesión de quién, se hizo uso de estas Foreign Keys para mantener toda la información necesaria sobre estos materiales y que no fuese posible perderles la pista. 

  # Decisiones técnicas tomadas y justificación de las soluciones implementadas
  
  El frontend de la aplicación, como se ha mencionado antes, se ha desarrollado con Vue 3 y Vite, herramientas que permiten crear una interfaz dinámica y organizar la aplicación mediante componentes y vistas. Durante mis prácticas y desarrollo del proyecto final, trabajé con esta tecnología, por lo que no representaba una curva de aprendizaje importante para la realización de esta aplicación web.

  Para la navegación entre pantallas he utilizado Vue Router, lo que me permite separar las vistas de la aplicación, como el inicio de sesión, el registro, el listado de materiales, etc. La gestión de la sesión en el frontend la he hecho con Pinia, una librería oficial diseñada para ser intuitiva y ligera, aquí me centralicé en controlar los datos del usuario autenticado y en la comprobación de los mismos para realizar las diferentes acciones disponibles en la página, con foco en el área de devolución de material.  

  El backend se ha realizado con PHP y MySQL/Mariadb, utilizando Xampp como entorno local. Esta elección parte de mi familiarización con ambos, el lenguaje y la herramienta, ya que durante el Ciclo de Desarrollo de Aplicaciones Web nos centramos en estas tecnologías a la hora de desarrollar. Además, PHP permite una arquitectura sencilla, donde sólo se encarga de recibir peticiones del front y devolver los datos, teniendo una buena comunicación con las bases de datos. 

  Aunque el sistema de sesiones sea sencillo, quise "protegerlo" por lo que realicé un hash sobre las contraseñas, para valorar la seguridad en la aplicación y la confianza. 

  El ámbito más importante de la aplicación fue el control de la tabla de *movimiento_material* ya que representaba el flujo y objetivo principal de la página, la trazabilidad de cada material. Aquí me encargué de asociar mediante claves foráneas, todos los factores e individuos que intervenían en un movimiento, para poder recuperar los datos de forma correcta sin que se perdiese nada, además, estas claves foráneas me ayudaron a mantener la integridad de los datos entre los distintos actores de la plataforma, utilicé eliminación en cascada sobre algunas para evitar registros "huérfanos". 

  En el frontend se ha utilizado Bootstrap y Bootstrap icons para crear una interfaz sencilla, amigable y responsive, además de que facilitaba las áreas de desarrollo, debido a que el diseño no resultaba tan largo gracias a esta herramienta. 
  
  
     
  
