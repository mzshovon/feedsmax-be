<?php

namespace App\Http\Middleware;

use App\Services\LoggerService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class QueryLoggerMiddleware
{
    protected $queries = [];
    protected $fileNames = [];
    protected $lineNumbers = [];
    protected $singleQueryTime = [];
    protected $totalTime = 0;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $queryLogStr = "";
        // Start listening to the queries
        DB::listen(function ($query) {
            $stack = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 50);
            $caller = $this->findRelevantCaller($stack);

            $this->fileNames[] = $caller['file'];
            $this->lineNumbers[] = $caller['line'];
            $this->queries[] = $query->sql;
            $this->singleQueryTime[] = $query->time;
            $this->totalTime += $query->time;
        });

        // Handle the request
        $response = $next($request);

        // Stop listening to the queries
        DB::flushQueryLog();

        // Iterate queries to set the table name and query time
        foreach($this->queries as $key => $query) {
            $table = getTableNameFromQuery($query) ?? "N/A";
            $queryLogStr .= "table_name {$table} | query_time {$this->singleQueryTime[$key]}ms | filename {$this->fileNames[$key]} | line_number {$this->lineNumbers[$key]} | ";
        }

        $queryLogStr .= "total_query_time {$this->totalTime}ms";
        $loggerService = app(LoggerService::class);
        $loggerService->append($queryLogStr);
        return $response;
    }

    /**
     * Find the most relevant caller from the stack trace.
     *
     * @param array $stack
     * @return array
     */
    protected function findRelevantCaller(array $stack)
    {
        foreach ($stack as $frame) {
            /** It removes the unnecessary stack file location ex.
             * [0] => "/vendor/..", [1] => "app/Repo/...", [2] => "/api/route...".
             * It will pick the actual path of executing sql query location [1] => "app/Repo/..."
             * For skipping fallback use end($stack)
             **/
            if (isset($frame['file']) && strpos($frame['file'], 'vendor') === false) {
                return $frame;
            }
        }
        return end($stack);
    }
}
