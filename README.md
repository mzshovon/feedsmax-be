# CFL Backend


## Contributors
- 

## Schema
[Schema Designer](http://www.laravelsd.com/share/GHs6Cp)

## RSA Key pair generate
```
$ openssl genrsa -out private_key.pem 2048(bit)
$ openssl rsa -in private_key.pem -out public_key.pem -pubout
```

## Grafana Regex

For Test:
```
{app="app-cfl-backend"} | regexp `\[.*\] test\.(?P<level>\w+): start_time_ms (?P<start_time_ms>\d+) \| end_time_ms (?P<end_time_ms>\d+) \| total_time_ms (?P<total_time_ms>\d+) \| request_body (?P<request_body>(?:\[\])|(?:\{.*\})) \| response_body (?P<response_body>\{.*\}) \| response_code (?P<response_code>\d+) \| url "(?P<url>[^"]*)" \| (?:x_request_id (?P<x_request_id>\w*))?`
```


For Production:
```
{app="app-cfl-backend-prod"} | regexp `\[.*\] production\.(?P<level>\w+): start_time_ms (?P<start_time_ms>\d+) \| end_time_ms (?P<end_time_ms>\d+) \| total_time_ms (?P<total_time_ms>\d+) \| request_body (?P<request_body>(?:\[\])|(?:\{.*\})) \| response_body (?P<response_body>\{.*\}) \| response_code (?P<response_code>\d+) \| url "(?P<url>[^"]*)" \| (?:x_request_id (?P<x_request_id>\w*))?`
```

For Nginx:
```
{job="app-cfl-slb-nginx"} |
pattern `<ip_address> - <time_local> "<method> <uri> <http_version>" <status> <body_bytes_sent> "<http_referer>" "<http_user_agent>" <request_url> <request_processing_time> <upstream_processing_time> <request_id>`
```
