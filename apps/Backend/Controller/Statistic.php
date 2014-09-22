<?php
namespace Backend\Controller;

class Statistic extends BackendBase {
    public function executeDefault() {
        $data = [
            'DEPOSITED' => [
                'title' => 'Đã đặt cọc'
            ],
            'BUYING' => [
                'title' => 'Đang mua'
            ],
            'NEGOTIATING' => [
                'title' => 'Đã đặt hàng'
            ],
            'NEGOTIATED' => [
                'title' => 'Chờ thanh toán'
            ],
            'BOUGHT' => [
                'title' => 'Đã mua'
            ],
            'SELLER_DELIVERY' => [
                'title' => 'Người bán giao'
            ],
            'RECEIVED_FROM_SELLER' => [
                'title' => 'Nhận từ người bán'
            ],
            'TRANSPORTING' => [
                'title' => 'Vận chuyển'
            ],
            'CHECKED' => [
                'title' => 'Đã kiểm'
            ],
            'WAITING_DELIVERY' => [
                'title' => 'Chờ giao'
            ],
            'CONFIRM_DELIVERY' => [
                'title' => 'YC giao'
            ],
            'DELIVERING' => [
                'title' => 'Đang giao'
            ],
            'RECEIVED' => [
                'title' => 'Đã nhận'
            ],
        ];

        $out = [];
        $stmt = \Order::read()->select('SUM(`total_amount`) as total,
                    SUM(real_payment_amount) AS payment,
                    SUM(real_refund_amount) AS refund,
                    SUM(`direct_fill_amount_vnd`) AS alipay,
                    `status`')
            ->groupBy('`status`')
            ->execute();

        while($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            if (isset($data[$row['status']])) {
                $data[$row['status']] = [
                    'title' => 'Đơn hàng ' .\Order::$statusTitle[$row['status']],
                    'total_amount' => $row['total'],
                    'total_payment' => $row['payment'],
                    'total_refund' => $row['refund'],
                    'direct_fill_amount' => $row['alipay']
                ];
            } else {
                $out[$row['status']] = [
                    'title' => 'Đơn hàng ' .\Order::$statusTitle[$row['status']],
                    'total_amount' => $row['total'],
                    'total_payment' => $row['payment'],
                    'total_refund' => $row['refund'],
                    'direct_fill_amount' => $row['alipay']
                ];
            }
        }

        $html = '';

        $sum = [
            'total_amount' => 0,
            'total_payment' => 0,
            'total_refund' => 0
        ];

        foreach($data as $status => $d) {
            $html .=
            "<tr>
                <td>{$d['title']}</td>
                <td>" .number_format(@$d['total_amount']) ."</td>
                <td>" .number_format(@$d['total_payment']) ."</td>
                <td>" .number_format(@$d['total_refund']) ."</td>
            </tr>";

            $sum['total_amount'] += @$d['total_amount'];
            $sum['total_payment'] += @$d['total_payment'];
            $sum['total_refund'] += @$d['total_refund'];
        }

        $html .=
            "<tr class='sum'>
                <td>Tổng</td>
                <td>" .number_format(@$sum['total_amount']) ."</td>
                <td>" .number_format(@$sum['total_payment']) ."</td>
                <td>" .number_format(@$sum['total_refund']) ."</td>
            </tr>";

        $html = <<<EOD
        <table width="70%" cellspacing="0" cellpadding="5" border="1" style="border-collapse:collapse;">
            <tr>
                <td><strong>Các đơn hàng</strong></td>
                <td><strong>Tổng giá trị đơn (bao gồm cả phí)</strong></td>
                <td><strong>Tổng khách thanh toán</strong></td>
                <td><strong>Tổng trả lại</strong></td>
            </tr>
            {$html}
        </table>
EOD;
        $h = '';
        foreach($out as $status => $d) {
            $h .="<strong>Tổng {$d['title']}</strong>: " .number_format($d['total_amount']) ."<br>";
        }

        $html .= $h;

        return $this->renderText($html);

    }
}