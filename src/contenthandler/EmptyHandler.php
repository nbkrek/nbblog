<?php

namespace nbkrnet\nbblog\contenthandler;

class EmptyHandler {
    public function renderHTML() {
        throw new \Exception('This is not a displayable content type.');
    }
}
