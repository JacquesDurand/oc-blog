<?php

declare(strict_types=1);

namespace App\Authentication;

 class Role
 {
     public const ROLE_REMOVED = 0;
     public const ROLE_AWAITING_VERIFICATION = 1;
     public const ROLE_USER_VERIFIED = 2;
     public const ROLE_ADMIN = 3;
 }
