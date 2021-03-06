<?php

namespace App;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Routing\RouteCollectionBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;

final class Kernel extends BaseKernel
{

    use MicroKernelTrait;

    private const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    /** @var array */
    private $modules = [];

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * {@inheritdoc}
     */
    public function shutdown()
    {
        parent::shutdown();
    }

    /**
     * @return iterable
     */
    public function registerBundles(): iterable
    {
        $contents = require $this->getProjectDir().'/config/bundles.php';
        foreach ($contents as $class => $envs) {
            if ($envs[$this->environment] ?? $envs['all'] ?? false) {
                yield new $class();
            }
        }
    }

    /**
     * @return string
     */
    public function getProjectDir(): string
    {
        return \dirname(__DIR__);
    }

    /**
     * @throws \Exception
     * @throws \InvalidArgumentException
     * @param LoaderInterface  $loader
     * @param ContainerBuilder $container
     */
    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $container->addResource(new FileResource($this->getProjectDir().'/config/bundles.php'));
        $container->setParameter('container.dumper.inline_class_loader', true);
        $confDir = $this->getProjectDir().'/config';

        $loader->load($confDir.'/{packages}/*'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/{packages}/'.$this->environment.'/**/*'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/{services}'.self::CONFIG_EXTS, 'glob');
        $loader->load($confDir.'/{services}_'.$this->environment.self::CONFIG_EXTS, 'glob');

        $this->configureContainerForModules($container, $loader);
    }

    /**
     * @throws \Symfony\Component\Config\Exception\LoaderLoadException
     * @throws \Symfony\Component\Yaml\Exception\ParseException
     * @param RouteCollectionBuilder $routes
     */
    protected function configureRoutes(RouteCollectionBuilder $routes): void
    {
        $confDir = $this->getProjectDir().'/config';

        $routes->import($confDir.'/{routes}/'.$this->environment.'/**/*'.self::CONFIG_EXTS, '/', 'glob');
        $routes->import($confDir.'/{routes}/*'.self::CONFIG_EXTS, '/', 'glob');
        $routes->import($confDir.'/{routes}'.self::CONFIG_EXTS, '/', 'glob');

        $this->configureRoutesForModules($routes);
    }

    /**
     * @param RouteCollectionBuilder $routes
     * @throws \Symfony\Component\Config\Exception\LoaderLoadException
     */
    private function configureRoutesForModules(RouteCollectionBuilder $routes)
    {
        foreach ($this->modulesConfig() as $name => $config) {
            $annotationsPath = $config['path'] . '/Resources/config/routes/';

            if (file_exists($annotationsPath)) {
                $routes->import($annotationsPath . '/*', '/', 'glob');
            }
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param LoaderInterface  $loader
     */
    private function configureContainerForModules(ContainerBuilder $container, LoaderInterface $loader)
    {
        foreach ($this->modulesConfig() as $name => $config) {
            if (is_dir($servicesPath = ($path = $config['path']) . '/Resources/config')) {
                $loader->import($servicesPath . '/{services}' . self::CONFIG_EXTS, 'glob');
            }

            if (file_exists($twigPath = $path . '/Resources/config/twig.yaml')) {
                $twigConfig = Yaml::parseFile($twigPath);

                if ($paths = ($twigConfig['twig']['paths'] ?? [])) {
                    $twigPaths = array_merge($twigPaths ?? [], $paths);
                }
            }
            if (file_exists($doctrinePath = $path . '/Resources/config/doctrine.yaml')) {
                $doctrineConfig = Yaml::parseFile($doctrinePath);

                if ($mappings = ($doctrineConfig['doctrine']['orm']['mappings'] ?? [])) {
                    $doctrineMappings = array_merge($doctrineMappings ?? [], $mappings);
                }
            }
            if (file_exists($translationPath = $path . '/Resources/config/translation.yaml')) {
                $translationConfig = Yaml::parseFile($translationPath);

                if ($paths = ($translationConfig['framework']['translator']['paths'] ?? [])) {
                    $translationPaths = array_merge($translationPaths ?? [], $paths);
                }
            }
        }

        ($twigPaths ?? false) && $container->loadFromExtension('twig', [
            'paths' => $twigPaths,
        ]);
        
        ($translationPaths ?? false) && $container->loadFromExtension('framework', [
            'translator' => [
                'paths' => $translationPaths,
            ],
        ]);

        ($doctrineMappings ?? false) && $container->loadFromExtension('doctrine', [
            'orm' => [
                'mappings' => $doctrineMappings,
            ],
        ]);
    }

    /**
     * @return array
     */
    private function modulesConfig(): array
    {
        if (!empty($modules = $this->modules)) {
            return $modules;
        }

        $mainModulesDirectory = $this->getProjectDir() . '/config/modules';
        $environmentModulesConfig = $mainModulesDirectory . '/' . $this->environment . '/modules.yaml';
        $mainModulesConfig = $mainModulesDirectory . '/modules.yaml';

        $modules = Yaml::parseFile(file_exists($environmentModulesConfig)
            ? $environmentModulesConfig : $mainModulesConfig
        );

        if (is_array($modules)) {
            foreach ($modules as $name => $config) {
                if (!($config['enabled'] ?? false) || empty($path = $config['path'] ?? null)) {
                    unset($modules[$name]);
                    continue;
                }

                $modules[$name]['path'] = str_replace('%kernel.project_dir%', $this->getProjectDir(), $path);
            }

            $this->modules = $modules;

            return $modules;
        }

        return [];
    }

}
