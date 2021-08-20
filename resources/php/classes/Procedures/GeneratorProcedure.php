<?php

class GeneratorProcedure extends Procedure
{
    private const INTERNAL_ERROR_MESSAGE_PREFIX = 'INTERNAL ERROR! ';

    public function run(): void
    {
        $config = $this->getOriginalJsonFileContentArray('generator-configuration.json');

        foreach ($config as $procedureId => $procedure) {
            try {
                $procedureId++;
                $class = $procedure['class'] ?? null;
                $params = $procedure['params'] ?? null;

                if (is_null($class)) {
                    $this->error("class cannot be null in procedure #$procedureId");
                } else if (!is_array($params)) {
                    $this->error("params must be array for class '$class' in procedure #$procedureId");
                }

                call_user_func_array(array(new $class(), 'run'), $params);
            } catch (GeneratorException $e) {
                //error printed in error method
            } catch (Throwable $e) {
                $this->print(self::INTERNAL_ERROR_MESSAGE_PREFIX . $e->getMessage());
            }
        }
    }
}
