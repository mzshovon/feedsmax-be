<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeServiceWithRepo extends Command
{
    protected $serviceDirPath = "Services";
    protected $repositoryPath = "Services/Contracts";
    protected $serviceBindConfigFileName = "service-bind";
    protected $interfaceToServiceBindingKeyName = "interface-to-service-array";
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service {name : The name of the service class} {--rp : Include repository} {--rm : Include repo methods}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new service class with optional repository interface.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if($this->confirm("Saving time equal more money 💸. Do you wish to continue?")) {
            $name = $this->argument('name');
            $includeRepo = $this->option('rp') ?? null;
            $includeRepoMethods = $this->option('rm') ?? null;
            [
                $serviceStorePath,
                $repositoryStorePath,
                $namespaceServicePath,
                $namespaceRepoSitoryPath,
                $name
            ] = $this->parseAndCheckForAdditionalPathForStore($name);

            // Check service dir exists or not
            if (!file_exists(app_path($serviceStorePath))) {
                mkdir(app_path($serviceStorePath));
            }

            $servicePath = app_path($serviceStorePath . "/{$name}Service.php");
            File::put($servicePath, $this->generateServiceClass($name, $includeRepo, $namespaceServicePath, $namespaceRepoSitoryPath, $includeRepoMethods));
            $this->updateFilePermission($servicePath);

            if ($includeRepo) {
                // Check repo dir exists or not
                if (!file_exists(app_path($repositoryStorePath))) {
                    mkdir(app_path($repositoryStorePath));
                }

                $repositoryPath = app_path($repositoryStorePath . "/{$name}ServiceInterface.php");
                $repositoryBuild = File::put($repositoryPath, $this->generateRepositoryInterface($name, $namespaceRepoSitoryPath, $includeRepoMethods));
                $this->updateFilePermission($repositoryPath);

                if($repositoryBuild) {
                    $configFile = config_path($this->serviceBindConfigFileName);
                    $interfaceToServicesArray = config("{$this->serviceBindConfigFileName}");
                    $interfaceToServicesArray[$this->interfaceToServiceBindingKeyName]["{$namespaceRepoSitoryPath}\\{$name}ServiceInterface"] = "{$namespaceServicePath}\\{$name}Service";

                    File::put("{$configFile}.php", "<?php \n\treturn " . var_export($interfaceToServicesArray, true) . ";");
                    $this->updateFilePermission("{$configFile}.php");

                }

            }

            $this->info("Great! Now you are very near to become rich! 😎");

        } else {
            $this->warn("So you decide to lose money! 🤐");
        }
    }

    /**
     * @param string $name
     * @param string $repo
     * @param string $namespaceServicePath
     * @param string|null $namespaceRepoSitoryPath
     * @param string|null|null $model
     *
     * @return string
     */
    protected function generateServiceClass(
        string $name,
        string $repo,
        string $namespaceServicePath,
        string|null $namespaceRepoSitoryPath,
        string|null $model = null
    ): string {
        // Customize this method to generate the service class content
        $classHeader = "<?php\n\nnamespace {$namespaceServicePath};\n\n" . ($repo ? "use {$namespaceRepoSitoryPath}\\{$name}ServiceInterface;\n\n" : "");
        $classBody = "class {$name}Service" . ($repo ? " implements {$name}ServiceInterface" : "") . "\n{\n    // Your service class code here\n";
        $classBody .= $model ? $this->modelMethods($name) : "";
        $classBody .= "}\n";
        return $classHeader . $classBody;
    }

    /**
     * @param string $name
     * @param string|null $namespaceRepoSitoryPath
     * @param string|null|null $model
     *
     * @return string
     */
    protected function generateRepositoryInterface(
        string $name,
        string|null $namespaceRepoSitoryPath,
        string|null $model = null
    ): string {
        // Customize this method to generate the repository interface content
        $interfaceHeader = "<?php\n\nnamespace {$namespaceRepoSitoryPath};\n\n";
        $interfaceBody = "interface {$name}ServiceInterface\n{\n    // Your repository interface code here\n";
        $interfaceBody .= $model ? $this->modelMethods($name, true) : "";
        $interfaceBody .= "}\n";
        return $interfaceHeader . $interfaceBody;
    }

    /**
     * It will make model method such get,store, update, delete and find
     * Then it will return into service and interface body if mentioned
     *
     * @param string $name
     * @param bool $interface
     *
     * @return string
     */
    protected function modelMethods(string $name, $interface = false): string
    {
        $methods = "";
        if (!$interface) {
            $methods .= "\tpublic function __construct(){}\n\n";
        }
        $methodInvoker = !$interface ? "{}\n\n" : ";\n\n";
        $methods .= "\tpublic function get()" . $methodInvoker;
        $methods .= "\tpublic function get{$name}ById(int " . '$id' . ")" . $methodInvoker;
        $methods .= "\tpublic function store(array " . '$request' . ")" . $methodInvoker;
        $methods .= "\tpublic function update(array " . '$request, int $id' . ")" . $methodInvoker;
        $methods .= "\tpublic function delete(int " . '$id' . ")" . $methodInvoker;
        return $methods;
    }

    /**
     * It will take string and check there is any directory with "/" char
     * If found then it will explode the string and unset() the last indexed string
     * Then it will generate recursive namespace path and dir path
     * If not found it will return paths as usual
     *
     * @param string $name
     *
     * @return array
     */
    protected function parseAndCheckForAdditionalPathForStore(string $name): array
    {
        $name = $name;
        $serviceStorePath = $this->serviceDirPath;
        $repositoryStorePath = $this->repositoryPath;
        $namespaceServicePath = "App\\" . $serviceStorePath;
        $namespaceRepoSitoryPath = "App\\" . $serviceStorePath . "\\Contracts";

        if (str_contains($name, "/")) {
            $parsePath = explode("/", $name);
            $name = $parsePath[count($parsePath) - 1];
            unset($parsePath[count($parsePath) - 1]);
            foreach ($parsePath as $value) {
                $namespaceServicePath .= "\\$value";
                $namespaceRepoSitoryPath .= "\\$value";
            }
            $serviceStorePath .= "/" . implode("/", $parsePath);
            $repositoryStorePath .= "/" . implode("/", $parsePath);
        }
        return [
            $serviceStorePath,
            $repositoryStorePath,
            $namespaceServicePath,
            $namespaceRepoSitoryPath,
            $name
        ];
    }

    private function updateFilePermission(string $filePath, int $permission = 777) : void
    {
        exec("chmod {$permission} -R {$filePath}");
    }
}
