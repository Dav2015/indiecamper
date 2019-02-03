# MinusTravel

# API DOCUMENTATION

This API allow to make a geographic searches of places for planning a trip.

# Features

* Search for the near highlight place
* Create a trip route
* Choose places by category

# Data structures

| Details | Description |
| --- | --- |
| `,` | Separate latitude from longitude -> Ex:38.6933996 `,` 9.2209565 |
| `;` | Separate two gps locations -> Ex:`38.6933996,9.2209565 `;` 41.3696861,9.2209565 |

| Atribute | Description |
| --- | --- |
| point | only one gps location -> Ex:" `?point=38.6933996,9.2209565` " |
| points | needs two or more gps locations ->Ex:" `?points=38.6933996,9.2209565;38.6933996,9.2209565` " |

# Category

Is a optional argument that makes the API only output places whit the given category<br>

At this moment exist only two categories

| Category | Example |
| --- | --- |
| nature | "/near `/nature/`?point=38.6933996,9.2209565" |
| city | "/route `/city/`?points=38.6933996,9.2209565;38.6933996,9.2209565" |

# Near

Search for the near highlight place

| Resource | Description |
| --- | --- |
| GET /near | latitude,longitude -> Ex:" `/near?point=38.6933996,9.2209565"` |
| GET /near[/{category}] | category is optional ->:Ex:" `/near/nature?point=...` |

Response

```sh
{
"placeID":"9",
"category":"nature",
"name":"Aqu\u00e1rio de Barcelona",
"country":"Spain",
"address":"Moll d'Espanya,del Port Vell, s\/n, 08039 Barcelona, Espanha",
"latitude":"41.3696861",
"longitude":"2.1507149",
"rate":"4",
"description":null,
"distance":"671.2135543725476"
}
```

# Route

Create a route whit places from start to point or whit intermidiate points

| Resource | Description |
| --- | --- |
| GET /route | Ex:" `/route?points=38.6933996,9.2209565;38.6933996,9.2209565"` |
| GET /route[/{category}] | category is optional ->:Ex:" `/route/nature?points=...` |

Response<br>

The response is a list of places between start to end point and intermidiate and points if exists.

```sh
[
{"placeID":"8",
"category":"nature",
"name":"Serra de Collserola",
"country":"Spain",
"address":"C4V2+2M Sol i Aire, Sant Cugat del Vall\u00e8s, Espanha",
"latitude":"41.4413741",
"longitude":"2.0744185",
"rate":"3",
"description":null,
"distance":"11.56883203409825"},

{"placeID":"9",
"category":"nature",
"name":"Aqu\u00e1rio de Barcelona",
"country":"Spain",
"address":"Moll d'Espanya, del Port Vell, s\/n, 08039 Barcelona, Espanha",
"latitude":"41.3696861",
"longitude":"2.1507149",
"rate":"4",
"description":null,
"distance":"11.872175756609721"}
.....
]
```

# Note

Feel free to change the gps coordinates, but inside iberian peninsula, there is no data out .
