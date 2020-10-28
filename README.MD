#PRODUCTS API

- Realizar el schema de base de datos de productos y variantes(MySQL)

- Obtenga un listado de productos y que contemple un paginado
```
/products/list/{page}
```
- Traer un solo producto 
```
/products/get/{id}
```
- Traer un solo producto y sus variantes (Ejemplo: Remera roja y sus otros colores)
```
/products/like/{id}
```
- Traer productos según busqueda (pasándola por Get)
```
/products/search/{param}/{value}
```
- Traer un solo producto por slug ( esto quiere decir que lo traiga por un identificador único que no sea un ID, sino un string)
```
/products/slug/{slug}
```
- Realizar un POST a una tabla de consultas de un producto en particular.
```
/products/post
```
