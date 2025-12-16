<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if ($input['type'] === 'visit') {
        // Track visitor
        $visitor = [
            'ip' => $_SERVER['REMOTE_ADDR'],
            'city' => $input['city'] ?? 'Unknown',
            'timestamp' => time(),
            'page' => $input['page'] ?? ''
        ];
        
        $visitors = file_exists('data/visitors.json') ? json_decode(file_get_contents('data/visitors.json'), true) : [];
        $visitors[] = $visitor;
        file_put_contents('data/visitors.json', json_encode($visitors));
        
    } elseif ($input['type'] === 'lead') {
        // Track lead
        $lead = [
            'name' => $input['name'] ?? '',
            'phone' => $input['phone'] ?? '',
            'date' => $input['date'] ?? '',
            'attraction' => $input['attraction'] ?? '',
            'adults' => $input['adults'] ?? '',
            'children' => $input['children'] ?? '',
            'timestamp' => time(),
            'ip' => $_SERVER['REMOTE_ADDR']
        ];
        
        $leads = file_exists('data/leads.json') ? json_decode(file_get_contents('data/leads.json'), true) : [];
        $leads[] = $lead;
        file_put_contents('data/leads.json', json_encode($leads));
    }
    
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error']);
}
?>