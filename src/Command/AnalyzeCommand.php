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
            ->addArgument('path', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $input->getArgument('path');
        $classes = $this->getClassNames($path);

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
        ] = $classes->analyzeMethods();

        [
            'number' => $number,
            'withDescription' => $withDescription,
            'withMultipleTypes' => $withMultipleTypes,
            'withWeakType' => $withWeakType,
        ] = $classes->analyzeMethodParams();

        [
            'number' => $methodParams,
            'withDescription' => $methodParamsWithDescription,
            'withMultipleTypes' => $methodParamsWithMultipleTypes,
            'withWeakType' => $methodParamsWithWeakType,
        ] = $classes->analyzeMethodParams();

        [
            'number' => $methodReturnStatements,
            'withDescription' => $methodReturnStatementsWithDescription,
            'withMultipleTypes' => $methodReturnStatementsWithMultipleTypes,
            'withWeakType' => $methodReturnStatementsWithWeakType,
            'withVoid' => $methodReturnStatementsWithVoid,
        ] = $classes->analyzeMethodReturnStatements();

        [
            'number' => $methodThrows,
            'withDescription' => $methodThrowsWithDescription,
            'withMultipleTypes' => $methodThrowsWithMultipleTypes,
        ] = $classes->analyzeMethodThrows();

        $table
            ->setHeaders(['', 'Total', 'With description', 'Multiple types', 'Weak types', 'Void'])
            ->addRow(['Properties', $properties, $this->withPercentage($propertiesWithDescription, $properties)])
            ->addRow(['Methods', $methods, $this->withPercentage($methodsWithDescription, $methods)])
            ->addRow(new TableSeparator())
            ->addRow([
                'Method params',
                $methodParams,
                $this->withPercentage($methodParamsWithDescription, $methodParams),
                $this->withPercentage($methodParamsWithMultipleTypes, $methodParams),
                $this->withPercentage($methodParamsWithWeakType, $methodParams),
            ])
            ->addRow([
                'Method return statements',
                $methodReturnStatements,
                $this->withPercentage($methodReturnStatementsWithDescription, $methodReturnStatements),
                $this->withPercentage($methodReturnStatementsWithMultipleTypes, $methodReturnStatements),
                $this->withPercentage($methodReturnStatementsWithWeakType, $methodReturnStatements),
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

    private function getClassNames($path): array
    {
        $declaredClasses = get_declared_classes();
        $this->loadFiles($path);
        $newClasses = array_diff(get_declared_classes(), $declaredClasses);

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
