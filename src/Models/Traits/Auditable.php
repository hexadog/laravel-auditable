<?php

namespace Hexadog\Auditable\Models\Traits;

trait Auditable
{
    use CreatedBy;
    use UpdatedBy;
    use DeletedBy;
}
