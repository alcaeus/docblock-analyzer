<?php declare(strict_types = 1);

namespace Alcaeus\DocblockAnalyzer\Command;

use Alcaeus\DocblockAnalyzer\Documentation\ClassCollection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class AnalyzeCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('analyze')
            ->addArgument('path', InputArgument::REQUIRED)
            ->addArgument('namespace', InputArgument::REQUIRED)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $input->getArgument('path');
        $namespace = $input->getArgument('namespace');
        $classes = $this->getClassNames($path, $namespace);

        $classes = ClassCollection::create($classes);

        $output->writeln(sprintf('Parsed %d classes.', count($classes)));

        $table = new Table($output);

        [
            'number' => $properties,
            'withDescription' => $propertiesWithDescription,
        ] = $classes->analyzeProperties();
        [
            'number' => $methods,
            'withDescription' => $methodsWithDescription,
            'withInheritDoc' => $methodsWithInheritDoc,
        ] = $classes->analyzeMethods();

        $table
            ->setHeaders(['', 'Total', 'With description', 'Inherits documentation'])
            ->addRow(['Properties', $properties, $this->withPercentage($propertiesWithDescription, $properties)])
            ->addRow(['Methods', $methods, $this->withPercentage($methodsWithDescription, $methods), $this->withPercentage($methodsWithInheritDoc, $methods)])
        ;
        $table->render();

        $table = new Table($output);

        [
            'number' => $methodParams,
            'withDescription' => $methodParamsWithDescription,
            'withMultipleTypes' => $methodParamsWithMultipleTypes,
            'withWeakType' => $methodParamsWithWeakType,
            'withTypedArray' => $methodParamsWithTypedArray,
        ] = $classes->analyzeMethodParams();

        [
            'number' => $methodReturnStatements,
            'withDescription' => $methodReturnStatementsWithDescription,
            'withMultipleTypes' => $methodReturnStatementsWithMultipleTypes,
            'withWeakType' => $methodReturnStatementsWithWeakType,
            'withTypedArray' => $methodReturnStatementsWithTypedArray,
            'withVoid' => $methodReturnStatementsWithVoid,
        ] = $classes->analyzeMethodReturnStatements();

        [
            'number' => $methodThrows,
            'withDescription' => $methodThrowsWithDescription,
            'withMultipleTypes' => $methodThrowsWithMultipleTypes,
        ] = $classes->analyzeMethodThrows();

        $table
            ->setHeaders(['', 'Total', 'With description', 'Multiple types', 'Weak types', 'Typed array', 'Void'])
            ->addRow([
                'Method params',
                $methodParams,
                $this->withPercentage($methodParamsWithDescription, $methodParams),
                $this->withPercentage($methodParamsWithMultipleTypes, $methodParams),
                $this->withPercentage($methodParamsWithWeakType, $methodParams),
                $this->withPercentage($methodParamsWithTypedArray, $methodParams),
            ])
            ->addRow([
                'Method return statements',
                $methodReturnStatements,
                $this->withPercentage($methodReturnStatementsWithDescription, $methodReturnStatements),
                $this->withPercentage($methodReturnStatementsWithMultipleTypes, $methodReturnStatements),
                $this->withPercentage($methodReturnStatementsWithWeakType, $methodReturnStatements),
                $this->withPercentage($methodReturnStatementsWithTypedArray, $methodReturnStatements),
                $this->withPercentage($methodReturnStatementsWithVoid, $methodReturnStatements),
            ])
            ->addRow([
                'Method throws',
                $methodThrows,
                $this->withPercentage($methodThrowsWithDescription, $methodThrows),
                $this->withPercentage($methodThrowsWithMultipleTypes, $methodThrows),
            ])
        ;

        $table->render();
    }

    private function getClassNames(string $path, string $namespace): array
    {
        $this->loadFiles($path);
        $newClasses = array_filter(get_declared_classes(), function (string $className) use ($namespace) {
            return preg_match('#^' . preg_quote($namespace, '#') . '#', $className);
        });

        return array_values($newClasses);
    }

    /**
     * @param $path
     *
     * @return void
     */
    private function loadFiles($path): void
    {
        if (is_file($path)) {
            require_once($path);
            return;
        }

        $directory = new \RecursiveDirectoryIterator($path);
        $iterator = new \RecursiveIteratorIterator($directory);
        $regex = new \RegexIterator($iterator, '/^.+\.php$/i', \RecursiveRegexIterator::GET_MATCH);

        foreach ($regex as $file) {
            $this->loadFiles($file[0]);
        }
    }

    private function calculatePercentage(int $part, int $whole): float
    {
        return $whole ? ($part / $whole) * 100 : 0;
    }

    private function withPercentage(int $part, int $total): string
    {
        return sprintf('%d (%.2f%%)', $part, $this->calculatePercentage($part, $total));
    }
}
