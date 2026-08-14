<?php

namespace App\Config\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // H2H routes (auth, inquiry, payment) don't use this filter
        $uri = $request->getUri()->getPath();
        $h2hRoutes = ['/h2h/auth', '/h2h/inquiry', '/h2h/payment'];
        
        foreach ($h2hRoutes as $route) {
            if (strpos($uri, $route) !== false) {
                return;
            }
        }
        
        // For all other routes, require auth
        $authHeader = $request->getHeaderLine('Authorization');
        if (empty($authHeader)) {
            return $this->unauthorized('Missing Authorization header');
        }
        
        if (!preg_match('/^Basic\s+(.*)$/i', $authHeader, $matches)) {
            return $this->unauthorized('Invalid Authorization format');
        }
        
        $decoded = base64_decode($matches[1]);
        if (strpos($decoded, ':') === false) {
            return $this->unauthorized('Invalid credentials');
        }
        
        [$username, $password] = explode(':', $decoded, 2);
        $expectedUser = env('H2H_API_USER', 'bankjateng');
        $expectedPass = env('H2H_API_PASS', 'puskesmas123');
        
        if ($username !== $expectedUser || $password !== $expectedPass) {
            return $this->unauthorized('Invalid credentials');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }

    protected function unauthorized($reason)
    {
        $response = service('response');
        $response->setStatusCode(401);
        $response->setHeader('WWW-Authenticate', 'Basic realm="H2H API"');
        $response->setJSON(['error' => $reason]);
        return $response;
    }
}
