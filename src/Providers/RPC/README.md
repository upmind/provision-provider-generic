# RPC Provider

This provider allows configuration of a base API URL and Authorization header for making remote procedure calls to a custom API.

## API Requests

API requests are made via HTTP POST to your configured base URL with JSON body. The function name is appended to the base URL, so for example if your base URL is `https://api.example.com/rpc` and you execute the `create` function, the provider will make a POST request to `https://api.example.com/rpc/create` with the an Authorization header value as configured.

When executing customFunction(), the `function` will be used as the RPC action, so for example if you execute `customFunction` with `function` parameter value of `doSomethingSpecial`, the provider will make a POST request to `https://api.example.com/rpc/doSomethingSpecial`.

## API Responses

All RPC API responses (except for terminate() which allows the omission of `data`) are expected to be JSON with the following structure:

- `success` (boolean) - Indicates whether the API request was successful or not
- `message` (string) - Message intended for end users describing the result of the API request, for example "Domain created successfully" or "Error: Invalid credentials"
- `data` (object) - Object containing response data
  - `service_id` (string) - Unique id of the service in your system
  - `service_identifier` (string|null) - Optional human-readable identifier for the service, for example a domain name
  - `service_status` (string|null) - Optional string describing the status of the service, for example "running", "suspended", "terminated" etc
  - `extra` (object|null) - Optional any additional relevant data you wish to return, for example usage metrics, expiry date etc

### Example RPC API Response

```json
{
  "success": true,
  "message": "Service created successfully",
  "data": {
    "service_id": "12345",
    "service_identifier": "example.com",
    "service_status": "running",
    "extra": {
        "foobar": "any additional relevant data"
    }
  }
}
```