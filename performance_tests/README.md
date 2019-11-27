Forget databases, use files!
============================

PHP Performance tests
---------------------

run the following commands in the php folder:

```bash
make run #to start the servers
make builddata #to generate the test data
make attack # to run warming and tests
```

The results after the warm up look similar to this:

```bash
============
DB WARMED
============
Requests      [total, rate, throughput]  3000, 301.20, 301.13
Duration      [total, attack, wait]      9.962607875s, 9.96005387s, 2.554005ms
Latencies     [mean, 50, 95, 99, max]    38.730871ms, 2.54069ms, 6.170701ms, 1.04629835s, 4.550779983s
Bytes In      [total, mean]              7700090, 2566.70
Bytes Out     [total, mean]              0, 0.00
Success       [ratio]                    100.00%
Status Codes  [code:count]               200:3000  
Error Set:
```

```bash
============
FILES WARMED
============
Requests      [total, rate, throughput]  3000, 300.10, 300.02
Duration      [total, attack, wait]      9.999179514s, 9.9966726s, 2.506914ms
Latencies     [mean, 50, 95, 99, max]    2.026858ms, 1.585685ms, 2.727981ms, 11.344551ms, 56.963833ms
Bytes In      [total, mean]              7523436, 2507.81
Bytes Out     [total, mean]              0, 0.00
Success       [ratio]                    100.00%
Status Codes  [code:count]               200:3000  
Error Set:
```