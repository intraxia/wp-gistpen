const prettier = require('prettier');

function createActionCall(j, name, args) {
  return j.exportNamedDeclaration(
    j.variableDeclaration('const', [
      j.variableDeclarator(
        j.identifier(name),
        j.callExpression(j.identifier('createAction'), args)
      )
    ])
  );
}

function getAndRemoveFromVariable(j, root, objectExpression) {
  const typeProperty = objectExpression.properties.find(
    property => property.key.name === 'type'
  );
  const typeName = typeProperty.value.name;
  const variable = root
    .find(j.VariableDeclarator, {
      id: {
        name: typeName
      }
    })
    .get();
  const init = variable.value.init;

  let parent = variable.parentPath;
  while (parent && parent.value.type !== 'ExportNamedDeclaration') {
    parent = parent.parentPath;
  }
  j(parent).remove();

  return init;
}

function createReplacement(j, root, source, name, func) {
  const args = [getAndRemoveFromVariable(j, root, source)];

  const payloadProperty = source.properties.find(
    property => property.key.name === 'payload'
  );
  const metaProperty = source.properties.find(
    property => property.key.name === 'meta'
  );

  if (payloadProperty || metaProperty) {
    args.push(
      j.arrowFunctionExpression(
        [j.identifier('resolve')],
        j.arrowFunctionExpression(
          func.params,
          j.callExpression(
            j.identifier('resolve'),
            [
              payloadProperty
                ? payloadProperty.value
                : j.identifier('undefined'),
              metaProperty && metaProperty.value
            ].filter(Boolean)
          )
        ),
        true
      )
    );
  }

  return createActionCall(j, name, args);
}

function handleFunctionDeclaration(j, root, node, $d, func) {
  const replacement = createReplacement(
    j,
    root,
    node.value.declaration.body.body[0].argument,
    node.value.declaration.id.name,
    func
  );

  $d.replaceWith(replacement);
}

function handleArrowFunctionExpression(j, root, node, $d, func) {
  const arrow = j(node)
    .find(j.ArrowFunctionExpression)
    .get();

  const replacement = createReplacement(
    j,
    root,
    arrow.value.body,
    node.value.declaration.declarations[0].id.name,
    func
  );

  $d.replaceWith(replacement);
}

module.exports = function transform(file, { jscodeshift: j }) {
  const root = j(file.source);

  // Add type-actions import
  root.find(j.Program).forEach(program => {
    program.value.body.unshift(
      j.importDeclaration(
        [j.importSpecifier(j.identifier('createAction'))],
        j.stringLiteral('typesafe-actions')
      )
    );
  });

  root
    .find(j.ImportDeclaration, {
      importKind: 'type'
    })
    .forEach(declaration => j(declaration).remove());

  root.find(j.ExportNamedDeclaration).forEach(node => {
    const $d = j(node);
    if (node.value.exportKind === 'type') {
      $d.remove();

      return;
    }

    if (node.value.declaration.type === 'FunctionDeclaration') {
      handleFunctionDeclaration(j, root, node, $d, node.value.declaration);
    }

    if (
      node.value.declaration.type === 'VariableDeclaration' &&
      node.value.declaration.declarations[0].init.type ===
        'ArrowFunctionExpression'
    ) {
      handleArrowFunctionExpression(
        j,
        root,
        node,
        $d,
        node.value.declaration.declarations[0].init
      );
    }
  });

  return prettier.format(root.toSource(), {
    singleQuote: true
  });
};

module.exports.parser = 'flow';
