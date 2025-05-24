<?php

namespace App\Services;

class LogObject
{
    private int $start_time;
    private int $end_time;
    private string $url;
    private array $request;
    private array $header;
    private string $x_request_id = "";
    private array $response;
    private ?int $response_code;
    private array|string $extra;
    private string $exception = "";
    private string $trace = "";
    private string $logLevel = "";

    /**
     * @param string $logLevel
     */
    public function setLogLevel(string $logLevel): void
    {
        $this->logLevel = $logLevel;
    }

    /**
     * @param int $start_time
     */
    public function setStartTime(int $start_time): void
    {
        $this->start_time = $start_time;
    }

    /**
     * @param int $end_time
     */
    public function setEndTime(int $end_time): void
    {
        $this->end_time = $end_time;
    }

    /**
     * @param array $request
     */
    public function setRequest(array $request): void
    {
        $this->request = $request;
    }

    /**
     * @param array $response
     */
    public function setResponse(array $response): void
    {
        $this->response = $response;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * @param int $response_code
     */
    public function setResponseCode(int $response_code): void
    {
        $this->response_code = $response_code;
    }

    /**
     * @param string $x_request_id
     */
    public function setXRequestId(string $x_request_id): void
    {
        $this->x_request_id = $x_request_id;
    }

    /**
     * @param string $exception
     */
    public function setException(string $exception): void
    {
        $this->exception = $exception;
    }

    /**
     * @param array $string
     */
    public function setStackTrace(string $trace): void
    {
        $this->trace = $trace;
    }

    /**
     * @return int
     */
    public function getStartTime(): int
    {
        return $this->start_time;
    }

    /**
     * @return int
     */
    public function getEndTime(): int
    {
        return $this->end_time;
    }

    /**
     * @return array|string
     */
    public function getExtra(): array|string
    {
        return $this->extra;
    }

    /**
     * @param array|string $extra
     */
    public function setExtra(array|string $extra): void
    {
        $this->extra = $extra;
    }

    /**
     * @return string
     */
    public function getRequest(): string
    {
        return json_encode($this->request);

    }

    /**
     * @return string
     */
    public function getResponse(): string
    {
        return json_encode($this->response);
    }

    /**
     * @return array
     */
    protected function wrap(): array
    {
        $data =  [
            "level" => $this->logLevel,
            "start_time_ms" => $this->start_time,
            "end_time_ms" => $this->end_time,
            "total_time_ms" => intVal($this->end_time - $this->start_time),
            "request_body" => $this->getRequest(),
            "response_body" => $this->getResponse(),
            "response_code" => $this->response_code,
            "url" => "\"{$this->url}\"",
        ];

        if(!empty($this->x_request_id)){
            $data["x_request_id"] = "{$this->x_request_id}";
        }

        if(!empty($this->exception)){
            $data["exception"] = "[{$this->exception}]";
        }

        if(!empty($this->trace)){
            $data["trace"] = "[{$this->trace}]";
        }

        if(!empty($this->extra)){
            if(gettype($this->extra) == "array") {

                foreach($this->extra as $key => $value) {
                    if(gettype($value) == "array")
                        $data["extra"][$key] = json_encode($value);
                    else
                        $data["extra"][$key] = "{$value}";
                }
            } else {
                $data["extra"] = "=> {$this->extra}";
            }
        }

        return $data;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        $data = $this->wrap();
        $str = "";
        foreach($data as $key => $value){
            $str .= $key." ".$value." | ";
        }
        return $str;
    }

    /**
     * @return string
     */
    public function toJson(): string
    {
        $data = $this->wrap();
        return json_encode($data, JSON_UNESCAPED_SLASHES);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $data =  $this->wrap();
        return $data;
    }

}
