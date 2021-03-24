-- print('Me encanta este lenguaje de programación: LUA')

-- name = "Enrique"

-- print('Hola,',name,'es el mejor!')

-- num = 10

-- if num % 2 == 0 then
--     print('El número ',num,' es par.')
-- else
--     print('El número ',num,'es impar.')
-- end

-- for i=0,100,2 do
--     if i % 3 == 0 then
--         print(i)
--     end
-- end

-- n = 5
-- suma = 0

-- for i=1,n do
--     suma = suma + i
-- end

-- print('El resultado de la suma es ',suma)

-- print('Escribe un número:')

-- num = io.read('*n')

-- print('Has escrito el número ', num)

-- math.randomseed(os.time())

-- for i=1,5 do
--     print('Número: ', math.random(1, 10))
-- end
-- text = ''

-- print('Dame una frase:')

-- text = io.read('*l')
-- print('El texto es: ')
-- for i=1,#text do
--     print(text:sub(i, i))
-- end

a = {}
n = 10
index = 1

for i=2,n*2,2 do
    a[index] = i
    index = index + 1
end
print('La longitud del array es: ',#a)
for i=1,#a do
    print(a[i])
end